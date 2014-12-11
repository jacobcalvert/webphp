### webphp

    <p>
    This project is inspired of the python libraries web.py and Tornado. I've created several things using
    those libraries and really like the way they work. For those unfamiliar with it, I'll give a (very) brief
    overview. You define URL routes via regex-like syntax and define a class that handles that mapping. The app
    then looks at what HTTP verb is used to access that URL, and called the appropriate class method (get, put, delete, etc.)
    </p>

    <p>
    I wanted something in PHP that would do the same. I found Laravel and other frameworks, but it was usually the case that these
    frameworks were extremely coupled to some design methodology. I wanted something that would be completely decoupled from any
    design framework. After I didn't find one that worked like I wanted it to, I decided I'd try to make it myself. 
    </p>

### Design and Implementation
    <p>
    The basic design use an .htaccess file to mod_rewrite the incoming URL to a server file that ultimately creates the WebPHP
    app object. The app then runs through its list of UrlMap objects and decides which UrlMap is the best match. After matching,
    the app creates a new instance of the handler class and calls its appropriate method for the HTTP verb that was used.
    The child class must implement whatever methods it wishes to use at the URL that it handles. For instance if you only
    want to have GET requests be valid on a certain handler, only define the get() method. If another HTTP verb is used to 
    access that URL, the parent WebRequest class will return a 501 (Not Implemented) response, and your class will never be hit.
    </p>


### Issues
* There is a StaticFileHandler class that can do text based stuff, but I have yet to figure out how to get it to do binary type like MPEG
* Getting the key=>value pairs from PUT, DELETE, etc. is a little tricky still. If the request is made using a form-data encoding as opposed to raw, I can't capture them
* I will certainly find more!!


Thanks for looking!