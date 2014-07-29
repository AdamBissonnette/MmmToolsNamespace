MmmTools
========

A set of tools for WordPress themes and plugins.  All html generated is designed to be used with bootstrap.

**Files included currently** July 29, 2014

- assets/

Contains js, css, and fonts used by this library

- lib/

Contains custom customizer classes (mmm-color-picker, adds a range input for setting color alpha)

- admin-tools.php

Includes a function to register the js, css, and fonts for use within a project

- data-tools.php

Includes basic functions for sql execution on the WordPress db, array to object / associative array, output functions for Mmm theme data arrays, misc data array manipulation functions (merge) and other misc functions that should be somewhere else.

- date-tools.php

IsWithinRange function: given a start date, end date and an optional third date (if not included we'll use the current date as our third date) we determine if the third date is within our start - end range.

- email-tools.php

Function to send an email via php

- html-tools.php

Functions to create various form elements.

- shortcodes.php

Assorted shortcodes.  Some deprecated.  Some bloated and unwiedly.

- string-tools.php

Function for turning an array of strings into a delimited list++.

- url-tools.php

Functions to get urls within the WordPress site - borrowed from another project!

- wp-tools.php

Admin, taxonomy and wordpress customizer control generation code.