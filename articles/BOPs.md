# BOP: Brads Overriding Principles.
Or "Basic" Overriding principle if the term catches on 😆

**These views are my own and do not necessarily reflect the views of the companies I work/consult for.**

## Top Level Error Handlers.
No matter what language you're using ALL errors should be captured
and clearly noticeable by developers. **No excuses.**

It's often nowhere near as complicated as people seem to believe.
I wrote an extensive article on this for PHP. _Link to come later._


## Errors are responses.
I am mainly talking about validation or business logic and UX here, however,
this also extends internal errors and DX (developer experience).

The main point is users should never see broken web pages because javascript was expecting some
missing data in a response. Or some blank nothing pages due to some internal error in PHP.

Nor should they see some meaningless error code that requires searching and clicking through an
endless cycle of links just to read some simple message that could have been displayed in the UI.
**I'm looking at YOU Microsoft! 👀**

Feedback can be technical if the user is a developer.
Eg. All shell scripts I build are for developers, so I show a stack trace.

## Developers are just as important as Users.
Continuing from above. Meaningful errors and stack traces should always be accessible to developers.

Never catch an error/exception just to throw a new one without including the source of the problem.

Detect developer environments and make errors visible where possible.
I conditionally include the stack trace in the API response/error pages
and sometimes I even construct a Jetbrains toolbox URL, that you can click to
have the IDE open the code at the exact line the error occurred.

Treat your fellow devs as first-class citizens and Users will also get the knock-on benefits of more efficient Engineers.

## Commandline Applications
I write an absolute ton of shell scripts and PHP command-line tools so it makes sense I have some specific rules for them too.

### CLI's should require input and explain themselves.
Nobody wants to memorize the millions of shell scripts I have written and handed out for various use cases.

So it should be safe to just type their name to see what they do.

Eg, my shell scripts often start something like this:
```shell
#!/usr/bin/env bash
source error-handler #yes I have an error handler for bash too 😅.

if [ -z $1 ];then
   echo "I perform X and Y for Z etc."
   echo "USAGE: `basename $0` <flags...> <operands...>"
   exit 1;
fi
```



