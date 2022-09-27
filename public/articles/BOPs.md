# BOP: Brads Overriding Principles.
Or "Basic" Overriding principle if the term catches on 😆

**These views are my own and do not necessarily reflect the views of the companies I work/consult for.**

## Top Level Error Handlers.
No matter what language you're using ALL errors should be captured
and clearly noticeable by developers. **No excuses.**

It's often nowhere near as complicated as people seem to believe.
I wrote an extensive article on this for PHP.
[Exception an Error Handling in PHP](2021-04-13-exception-and-error-handling-in-php.md)


## Errors are responses.
I am mainly talking about validation or business logic or anything the user can correct.
If some input is wrong just throw an exception with the User message instead of trying to pass
the message all the way up the stack.

The main point is user should never ever see broken interface because of some missing data or badly
formatted response.

Nor should they see some meaningless error code that requires searching and clicking through an
endless cycle of links just to read some simple message that could have been displayed in the UI.
**I'm looking at YOU Microsoft! 👀**

Feedback can be of a technical nature if your user is bound to be a developer or a power user.
Eg. All shell scripts I build are for developers, so I actually show a stack trace.

## Developers are just as important as Users.
Continuing on from above. Meaningful errors and stack traces should always be accessible to developers.

Never catch an error/exception just to throw a new one without including the source of the problem.

I normally don't agree with catch and re-throw behaviour, but it is needed sometimes and when that's
the case you should always provide devs access to the original error.
In PHP this is as simple as shoving the previous exception in the new one.

Detect developer environments and make errors visible where possible.
For WebApps I conditionally include stack trace in the API response/error page
and even sometimes I even construct a Jetbrains toolbox URL, that you can click to
have the IDE open the code at the exact line the error occurred.

Treat your fellow devs as first class citizens and Users will also get the knock on benefits more efficient Engineers.

## Shell scripts.
### Scripts should explain themselves and require input to perform an action.
A core principle I impose on myself whenever I write shell scripts is to start each one similar to this:
```shell
#!/usr/bin/env bash
source error-handler

if [ -z $1 ];then
	echo -e "USAGE: `basename $0` <flags...> <operands...>"
	exit 1;
fi
```

Nobody wants to memorize the millions of shell scripts I write for various use cases, so I make sure they are
self-explanatory.

And if they are for developers I always include the stack trace if like so:
```bash
# HALT EXECUTION WHEN ANY COMMAND FAILS AND PROVIDE A STACK TRACE.
#   Full stack trace only works if every script in the execution chain includes this script.
set -eE
trap 'catchError $LINENO $COMMAND' ERR
catchError() {
  echo -e "TRACE: `basename $0` failed in `readlink -f $0` on line $1"
  exit $?
}
```
I make this a script on its own and include it from all my others.
