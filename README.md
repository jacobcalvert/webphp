### webphp

This project is inspired of the python libraries web.py and Tornado. I've created several things using
those libraries and really like the way they work. For those unfamiliar with it, I'll give a (very) brief
overview. You define URL routes via regex-like syntax and define a class that handles that mapping. The app
then looks at what HTTP verb is used to access that URL, and called the appropriate class method (get, put, delete, etc.)


I wanted something in PHP that would do the same. I found Laravel and other frameworks, but it was usually the case that these
frameworks were extremely coupled to some design methodology. I wanted something that would be completely decoupled from any
design framework. After I didn't find one that worked like I wanted it to, I decided I'd try to make it myself. 

### Design and Implementation

The basic design use an .htaccess file to mod_rewrite the incoming URL to a server file that ultimately creates the WebPHP
app object. The app then runs through its list of UrlMap objects and decides which UrlMap is the best match. After matching,
the app creates a new instance of the handler class and calls its appropriate method for the HTTP verb that was used.
The child class must implement whatever methods it wishes to use at the URL that it handles. For instance if you only
want to have GET requests be valid on a certain handler, only define the get() method. If another HTTP verb is used to 
access that URL, the parent WebRequest class will return a 501 (Not Implemented) response, and your class will never be hit.

### Features
* Inheritable WebRequest class to provide basic API implementation quickly
* Ability to force SSL on a URL map
* Ability to force SSL on all URLs using a specific handler
* Template system that is completely detached from the rest of the implementation.


### Issues
* Getting the key=>value pairs from PUT, DELETE, etc. is a little tricky still. If the request is made using a form-data encoding as opposed to raw, I can't capture them
* Relies on .htaccess for the rewrite magic
* Cannot seek inside media files like mp4. But they play!
* I will certainly find more!!


### Updates
* Added ability to force SSL (see the example in the url_maps.php file) for a UrlMap [12/11/2014]
* Added pre_init and post_init methods so a child class can do extra initialization without rewriting the constructor [12/12/2014]
* Added example for forcing ssl on every URL that uses a specific handler [12/12/2014]
* Added template system and examples [12/30/2014]
* Can play media files like mp4s [12/30/2014]
Thanks for looking!