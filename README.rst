==================
WebSpriteGenerator
==================

About
=====

The WebSpriteGenerator (short: wsg) is an application to automate the creation
of so called sprite images and corresponding definition files. The application
is written completely in PHP__.

__ http://php.net


Scope
=====

This document gives a general overview about the WebSpriteGenerator
application. Usage examples are provided and the main interface is explained in
detail. Part of this explanation is a overview about the internal application
design. This information is only provided to a degree where it is needed to
understand the different choices the user has in running this application.
This is not a design document featuring implementation details about interfaces
or inner workings.


Clarification of terms
======================

Sprite image
------------

Sprite images are image files containing more than one image in some sort of
positioning scheme.

For example a message on a html page can have three images to indicate its
state. An error image, a question image and an information image. Instead of
storing these pictures in 3 different images, they are stored aligned in
someway one image file. This image file is considered a sprite.

Input definition file
---------------------

An input definition file is a arbitrary structured file. which maps image
filenames to classes. These classes may be CSS classes or any sort of
identifier (Whereas the default components of wsg assume the classes are valid
CSS class strings.).  An example would be something like ``images/foo.png =>
.some_class``. The format of the file does rely on the used DefinitionReader
implementation. (Detailed in `DefinitionReader`_).


Sprite/Output definition file
-----------------------------

As a sprite image is quite useless without some kind of information about the
positions and dimensions of the pictures stored in it a definition file is
needed.

This definition may be of an arbitrary format. To be used for webpages the
mostly used format is a CSS-background-definition using the background position
values to indicate the position of the image to use inside the sprite. Together
with this a absolute width and height of the defined element is used in most
cases to reflect the dimensions of the stored image, to not display any sprite
which might be positioned next to it.


Commandline usage
=================

Wsg's primary way of usage is commandline frontend. As wsg will most likely be
called by some sort of build system this seemed to be most natural way to
provide a user interface. The implementations of user interfaces is however
easily possible.

The commandline executable is located under ``src/bin/wsgen``. It provides a
lot of different options to configure the different components wsg is supposed
to use during processing. To allow for flexibility when it comes to input and
output definition formats, as well as the layout (positioning) of images inside
the sprites itself, the application consists of many different loosely coupled
components. These components may easily replaced by your own implementation if
some sort of special functionality is needed. Before the different components
and their function is described a detailed look at the commandline interface is
given.

After calling the ``wsgen`` executable a quite sophisticated help text is
displayed::

    WebSpriteGenerator 
    (c) 2010 Jakob Westhoff

    Usage:
      wsgen [OPTION, ...] <input-definition> <output-sprite> <output-definition>

    Options: 
      -h/--help                    Show this help text

      -r/--reader=<value>          Definition reader to use for input processing.
                                   (Default: css-like)

      -e/--render-engine=<value>   Renderer engine to use for sprite generation.
                                   (Default: gd)

      -w/--writer=<value>          Definition writer used to create an output
                                   definition. (Default: css-background)

      -l/--layout-manager=<value>  Layout manager used to calculate the placement
                                   of the images inside the sprite. (Default:
                                   color-group)


    Reader:
      css-like:   Reader of a css-like definition format. See the README.txt for
                  more information about this format.

      php-array:  Reader of a php based array structure. See the README.txt for
                  more information about this format.


    Writer:
      css-background:  Write out a css file containing moved background statements
                       for each sprite image.


    LayoutManager:
      color-group:  Group images horizontally by their overall color, to allow for
                    better png compression.

      vertical:     Align all images in a vertical row


    Renderer:
      gd:  Renderer using the GD lib, which is available in most default php
           installations.


The help text does not only show the placement of the arguments to be given, it
lists all available option switches, which may be used to select other
components to fulfill certain tasks of the processing cycle. Furthermore for
each of the different components a list of available implementations is
provided with short descriptions for each of them.

Every component does have a default implementation pre-selected in case a
special one is not selected. Therefore the easiest way to call the wsgen
commandline interface is something like this::

    bin/wsgen input.cfg sprite.png sprite-definition.css

The commandline shown above instructs wsg to read a file called ``input.cfg``
as input definition to create a sprite image called ``sprite.png`` containing
all of the images defined in there. Furthermore a sprite definition will be
written to the file ``sprite-definition.css``.

By default the input definition is assumed to be in a css-like format (Detailed
in `CSS-Like DefinitionReader`_), the sprite is rendered using the GD library
which is available in most PHP installations (Detailed in `GD Renderer`_),
whereas the output definition will be written as a CSS file which uses
`background` properties for mapping to the correct image in the sprite file
(Detailed in `CSS-Background DefinitionWriter`_). The placement of the
different images inside the sprite is determined by their color. This allows
for images with similar colors to be grouped together for a better PNG
compression (Detailed in `Color-Group LayoutManager`_).

All of these default behavior can be easily changed using the ``--reader``,
``--render-engine``, ``--writer`` and ``--layout-manger`` options. The options
are given followed by an equal sign followed by one of the possible
implementations listed in the corresponding section of the help text.

For example to use the `Vertical LayoutManager`_ instead of the default
`Color-Group LayoutManager`_ the following commandline could be issued::

    bin/wsgen --layout-manager=vertical input.cfg sprite.png sprite-definition.css


Components
==========

To allow for the greatest possible flexibility, when it comes to extension and
behavioral change of the application, it is designed to consist of loosely
coupled components, which are defined by strict interfaces to allow for
multiple different implementations of each component.

The application consists of 5 components:

- Logger
- DefinitionReader
- LayoutManager
- DefinitionWriter
- Renderer


Logger
------

The ``Logger`` components main obligation is to take message from any other
component and propagate them in a specific way, to inform the user about the
action taken. 


Console Logger
^^^^^^^^^^^^^^

The ``Console Logger`` is an implementation of the ``Logger`` component, which
simply outputs all provided log messages to the terminal console. They are
optionally formatted with a timestamp and the error-level assigned to the
message.

This is the default logger for the commandline interface.


DefinitionReader
----------------

The input needed by wsg consists of a mapping between image filenames and
identifiers. These identifiers are, even though this is not enforced are
supposed to be valid CSS identifiers.

This mapping might be stored in any kind of format. Therefore wsg uses the
``DefinitionReader`` component to read this mapping into a internal data
structure for application wide usage. Different implementations of the
``DefinitionReader`` therefore read different representations of the needed
configuration data.


PHP-Array DefinitionReader
^^^^^^^^^^^^^^^^^^^^^^^^^^

Because the whole application is implemented in PHP it seemed natural to
implement a definition file format utilizing PHP data structures, which may
directly be used internally.

The ``PHP-Array DefinitionReader`` is capable of using these array
representations as input.

The input definition files are supposed to obey to the following structure::


     <?php
       return array( 
         'image/file/1.png' => array( 
           '#css .rule',
           '#optionally another.css:rule',
            ...
         ),
         ...
       );

This datastructure might be useful if your definition is auto-generated by
some other PHP script, as it could be easily written using the ``var_export``
functionality. If you intend to write the definition files by hand, the
`CSS-Like DefinitionReader`_ might be a better choice.


CSS-Like DefinitionReader
^^^^^^^^^^^^^^^^^^^^^^^^^

The ``CSS-Like DefinitionReader`` is provides an easy to learn, as well as
familiar looking format, to anyone who has worked with CSS before. Its main
goals are readability as well as the chance to easily write definition files by
hand.

The definition files need to obey the following structure::

    some.css:rule,
    more.than:one > rule#is.possible {
      image: /path/to/image/file.png;
    }
    ...

It is quite similar to CSS taken into account, that CSS doesn't know the
``image`` property. Please note that the semicolon on the end of the property
line is not optional. It is mandatory. You will receive a parse error, if this
character is not supplied. An arbitrary amount of different rules might be
connected to one image as far as these rules are splitted by a comma (``,``).

All newlines and spaces are optional and may be left out or added in any amount
the author considers necessary.


LayoutManager
-------------

``LayoutManagers`` are responsible for positioning all the given image files
inside the sprite image, as well as calculating the needed sprite resolution.

At a first glance different sorts of ``LayoutManagers`` seem a little bit
useless, as simply aligning the images vertically or horizontally seems the
most effective and yet easiest way. Image positioning inside the sprite may
however directly influence the file size of the sprite. Due to different image
aspects, like PNG compression, the smallest image resolution does not always
produce the smallest files. Therefore an easy way to allow for different
positioning algorithms is provided by ``LayoutManagers``.


Vertical LayoutManager
^^^^^^^^^^^^^^^^^^^^^^

The ``Vertical LayoutManager`` uses one of the most naive approaches for
positioning the images inside the sprite. As the name already says they are
simply aligned in down one column vertically. The images are drawn below each
other.


Color-Group LayoutManager
^^^^^^^^^^^^^^^^^^^^^^^^^

The ``Color-Group LayoutManager`` calculates a certain metric for every supplied
picture to compare the used colors inside these pictures with each other. It
finally aligns pictures in groups of similar colors. Each of this groups is
drawn into its own row inside the picture.

Even though this might create a lot of unused free pixel space on the edges of
the sprite, the image sizes of the generated sprites are about 7-10% smaller to
their vertically aligned counterparts. This effect is caused by the compression
algorithm used by PNG images. It allows rows of similar color to be compressed
much better than rows with great changes in the color. The free pixel space
however can be compressed optimally as it is all the same color. Therefore it
can be neglected.

Because of the size benefits the ``Color-Group LayoutManager`` is selected by
default.


DefinitionWriter
----------------

Sprite images on their own are mostly useless in most cases, as no information
about where the images are located in the sprite is available. To provide this
information ``DefinitionWriters`` are used. The implementations of this
component supply the information about position and size of each of the images
inside the created sprite in some arbitrary data format.


CSS-Background DefinitionWriter
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Sprite images are used in conjunction with webpages/application in most of the
cases. The usually taken approach in this environment is to used CSS rules for
the needed images, which make use of the ``background-image`` property to
display a certain part of the sprite image. To specify the resolution of the
original image fixed ``width`` and ``height`` values are normally used. The
position of the image inside the sprite itself is selected using the
``background-position`` property.

The ``CSS-Background DefinitionWriter`` creates a file filled with valid CSS
rules, mapping each of the identifiers given to the `DefinitionReader`_ to a
bunch of CSS rules specifying the correct ``background-image`` and
``background-position`` to display the image inside the sprite file.


Renderer
--------

A lot of different libraries exist in the PHP world to draw images (GD,
IMagick, Cairo, ...). Furthermore a lot of different image formats exist out
there. Even though PNG is most likely to be used for sprite maps today. You
might want to use some other format. Or the input format for your sprites is
some rarely used format, which is not supported by the default libraries.

To overcome all of these problems the ``Renderer`` component was created. Each
of its implementations may use a completely different output format or a
different kind of library to do its drawing.


GD Renderer
^^^^^^^^^^^

The GD library is shipped with PHP and therefore available on most of the
systems using it. Even though it is by far not the most efficient,
user-friendly or feature-packed of the available solutions it provides all the
necessary facilities to be used for sprite generation.


Supported operating systems
===========================

The application has only been tested on \*nix based operating systems, where it
runs flawlessly. It has not been developed nor tested for the windows operation
system. However it should work there as well. If you can test the application
on windows any feedback is welcome. I will happily apply patches and tests for
the windows platform. I will however not create these fixes myself, as I don't
have the proper means to debug this application on a windows based system.


Supported PHP versions
======================

The application makes heavy usage of the features newly available in PHP 5.3,
like closures and namespaces. Therefore its minimum required version is 5.3.x.
There are currently no plans to create a backport, which will work with PHP
5.2.x.

Unit tests
==========

The application is fully unit tested. The tests are written utilizing
phpUnit__. To run the testsuite for the application a call to the ``runTests``
script should do the trick.

__ http://phpunit.de
