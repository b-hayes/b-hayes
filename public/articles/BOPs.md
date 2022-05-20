# BOP: Brads Overriding Principles.
Or "Basic" Overriding principle if the term catches on 😆

**These views are my own and do not necessarily reflect the views of the companies I work for.**

## Top Level Error Handlers.
No matter what language you're using ALL errors should be captured
and clearly noticeable by developers. **No excuses!**

All errors are catchable all it's often nowhere near as complicated as people seem to believe.

I wrote an extensive article on this for PHP. _Link to come later._ to help those still living in the php5 mindset.

## Errors are responses.
I am mainly talking about validation or business logic and UX here however,
this also extends internal errors and DX (developer experience).

The main point is user should never ever see broken web pages because javascript was expecting some
missing data in a response. Or some blank nothing pages due to some internal error in Php.

Nor should they see some meaningless error code that requires searching and clicking through an
endless cycle of links just to read some simple message that could have been displayed in the UI.
**I'm looking at YOU Microsoft! 👀**

Feedback can be of a technical nature if a developer is the user.
Eg. All shell scripts are for developers, so I actually show a stack trace for them.

As I mentioned all errors are catchable and shame on you if you
just let your apps break and say nothing to the user.

## Developers are just as important as Users.
Continuing on from above. Meaningful errors and stack traces should always be accessible to developers.

Never catch an error/exception just to throw a new one without including the source of the problem.

I normally don't agree with catch and re-throw behaviour, but it is needed sometimes.
In PHP this is as simple as shoving the previous exception in the new one you have created and other languages will
also have ways to pass information along with your re-thrown errors for you custom handlers etc.

Detect developer environments and make errors visible where possible.
For WebApps I conditionally include stack trace in the API response/error page
and even sometimes I even construct a Jetbrains toolbox ulr so they can click a link and
have their IDE open the code at the exact line the error occurred.

Treat your fellow devs as first class citizens and User will get the knock on benefits more efficient
and happy Engineers.

## Shell scripts.
### Scripts should explain themselves and require input to perform an action.
A core principle I impose on myself whenever I write shell scripts is to start each one similar to this:
```shell
#!/usr/bin/env bash
source error-handler #this line relates to BOP #1

if [ -z $1 ];then
	echo -e "USAGE: `basename $0` <flags...> <operands...>"
	exit 1;
fi
```

Nobody wants to memorize the millions of shell scripts I write for various use cases.

