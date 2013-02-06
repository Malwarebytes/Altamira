About
===================


A PHP abstraction library for JavaScript charting.


Getting Started
===================

To run the example, you need the following dependencies:


Jquery - http://jquery.com
JQPlot or Flot - http://www.jqplot.com ; http://www.flotcharts.org 
Flot Bubbles - https://github.com/ubermajestix/flot-plugins
NVD3 - A charting framework that uses D3.js


Unpack all the files into php enabled web server such as apache or nginx.

Run the following commands to get the javascript dependencies.

```bash
mkdir js
git clone git://github.com/flot/flot.git js/flot
git clone git://github.com/jonmchan/jqplot.git js/jqplot
git clone https://github.com/ubermajestix/flot-plugins js/flot-bubbles
git clone https://github.com/novus/nvd3.git js/nvd3
```

Load example.php in your browser.

API Documentation
=======================
Api Documentation is available using [phpDocumentor](http://www.phpdoc.org/).

Install phpDocumentor with PEAR using the instructions on the site. 
In order to generate documentation, use the following command:

    phpdoc -d src -t docs

You can now view documentation in your browser at localhost/Altamira/docs 
(assuming you can do that with localhost/Altamira/example.php).