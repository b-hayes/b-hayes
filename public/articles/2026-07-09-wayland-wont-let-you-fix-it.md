# Wayland Won't Let You Fix It

*How "you shouldn't have to" turned into "you're not allowed to."*

## I've built the same setup three times

Windows first, with AutoHotkey. Then Linux on X11. Then Linux again on Wayland — from scratch, because half the tools I'd leaned on the first two times just didn't exist any more.

Each rebuild did *less* than the one before it. Not because I wanted less. Because the thing underneath me kept quietly taking options off the table.

That's the whole reason I'm writing this. I've lost count of the evenings I've burned trying to fix something on this laptop — trackpad, focus, window management, the desktop falling over — and every single one ended in the same spot. Not "this is tricky." Not "you need some obscure config." It's always: *this can't be done, on purpose, and here's a paragraph on why you shouldn't have wanted it anyway.*

Same blocker every time. Wayland.

## The excuse, said out loud

Wayland has one big justification, and to be fair it sounds reasonable: *you shouldn't have to hand-tune low-level junk to get sane defaults.* Palm rejection should just work. The compositor should just do the right thing. You shouldn't need a config file to stop your cursor jumping around while you type.

Yep. As a **default**, that's correct. Software should be good out of the box. No argument from me.

## What actually happens

Here's the problem. That's only a principle if it points both ways, and it doesn't. It only ever gets aimed in one direction — at *taking control off you* — and never at the pile of new work Wayland dumped in my lap.

Look at what the switch actually cost me, none of which "you shouldn't have to" was ever applied to:

- I **had to rebuild my whole setup** when the interfaces I relied on got dropped.
- I **get logged out by crashes.** On Wayland the compositor *is* the display server. Shell dies, every window dies with it. There's no restarting it in place. On X11 the shell was disposable — it fell over, restarted, and my windows just sat there unbothered. That safety net is gone and nothing replaced it.
- I'm **forced to install third-party extensions** to claw basic behaviour back — and on GNOME those run as live code *inside* the shell process, so one stale or buggy one takes down my entire session. The only escape hatch is also a loaded gun.

Funny how nobody ever said "users shouldn't have to rebuild their setup when we bin an interface." *That* burden was fine. The work didn't vanish — it moved onto me, and then they took away the tools I'd have used to deal with it. Worst of both worlds.

## The one that started this: a trackpad deadzone

The example that set me off.

My trackpad's huge, my palm brushes it while I type, and the cursor jumps. On X11 the fix was ancient and boring: the Synaptics driver let you carve the pad down to a smaller live area — `synclient AreaTopEdge`, `AreaLeftEdge`, done. A deadzone. Five minutes.

libinput — what Wayland uses for input — **flat out doesn't do this.** They pulled area restriction on the theory that automatic palm detection should handle big trackpads for you. And when it works, great, you never think about it. When it *doesn't*, here's your menu:

- no setting,
- no `synclient` equivalent,
- no "I know what I'm doing" flag,
- and no GNOME extension can save you either, because extensions sit *above* libinput and never even see the raw finger data.

Capability gone, replaced by a guess, and when the guess is wrong the official answer is "adapt." That's not fewer knobs. That's no way out.

## Fine, let's measure it

I don't want to hand-wave this, so I took it all the way down on my actual hardware.

The pad's an I²C device (`093A:3014`). udev will happily tell you its size:

```
ID_INPUT_WIDTH_MM=149
ID_INPUT_HEIGHT_MM=97
```

**149mm × 97mm.** Basically a 16" MacBook Pro pad. Enormous. And the keyboard sits right above it, so when my fingers are on the home row, my palms are *on the trackpad.* I can't slide the keys up. I can't type with my hands curled into claws. Touching it isn't a choice, it's geometry.

What happens: a light, finger-sized bit of palm clips the top edge, and because it's a clickpad with taps on, that counts as a tap-click — no cursor movement needed — and my text cursor teleports. One stray tap and I'm suddenly typing in the wrong place.

Now watch every possible fix die, each for its own separate reason, all in the same grave:

- **Edge palm detection** — literally built for "palm hanging over the edge." Needs the pad to report its size. Mine does (see above), so it's **already on, full strength** — and still doesn't catch me. The edge-zone width? **Not a setting.** Nothing to turn up.
- **Size/pressure thresholds** (`AttrPalmPressureThreshold`, `AttrPalmSizeThreshold`) — these catch big, heavy contacts. Mine are small and light. To catch them I'd have to drop the thresholds so far they'd eat my real taps too. Pointless.
- **Disable-while-typing** — already on. It only blocks touches that *start* while I'm typing; it does nothing about a palm that's *already resting* when a tap fires in a gap between keystrokes. And the timeout isn't adjustable.
- **Turn off tap-to-click** — kills the stray taps, sure, but also kills tap-and-drag, and on a stiff clickpad dragging by physically holding the pad down is miserable. No.
- **An active-area deadzone** — the one thing that'd actually nail it: ignore the top strip where my palms live. Exactly what `AreaTopEdge` did on X11. Removed on purpose. **Gone.**

That's the entire board. Every surviving option is either already maxed out or breaks something I need, and the one that fits perfectly was deliberately deleted. There's no clean software fix here. I checked. It's dead.

### So what do you actually do

Since the platform won't *fix* it, everything left is either outside Wayland's reach or outside software entirely:

- **Plugged into a mouse:** `send-events` → `disabled-on-external-mouse`. Pad goes dead the second a mouse is connected and wakes up on its own when you unplug. This is the one genuinely clean win — and the only reason it works is that it dodges the whole input-tuning mess instead of trying to tune it.
- **On the move:** the only *real* deadzone left is a physical one. A strip of non-conductive tape (electrical, gaffer, matte) along the top edge. Capacitive pads can't feel through a thick enough non-conductive layer, so the taped strip is a hard deadzone that libinput can't argue with. It's ugly. It's also the single most reliable thing in this entire article, and it works *precisely because* it happens below the layer that's been locked against me.

Sit with that for a second. On a 2025 laptop, my most dependable trackpad fix is a bit of tape — because the software that used to offer it now refuses, and calls that progress.

## The trackpad was just the one I was holding

Honestly? The trackpad is the *small* one. It's just the fight I happened to be having when I started writing this down. Every other thing I've tried to fix on this machine ended in the same place, for the same reason. So here's the pile.

### Focus stealing: the window that won't come forward

I click a link in my terminal. The browser opens the tab — somewhere behind my editor. I don't see it. So I click the link again. And again. Twenty minutes later I've got fifty tabs stacked up behind my IDE and I look like the idiot who kept clicking.

I'm not the idiot. Wayland is.

Mutter has "focus-stealing prevention": an app is only allowed to raise itself if it's holding a valid `xdg-activation` token proving *you* just did something to trigger it. Sounds reasonable. Falls over the moment the app that triggered the action (my JetBrains terminal, running through XWayland) doesn't hand a token across — which it doesn't. So the browser has every right to come forward, and Wayland refuses to let it, because the paperwork wasn't in order.

There is no "just let my windows come to the front" setting. No allowlist. The only fix was to install a third-party GNOME extension whose entire job is to catch the "this window wants attention" signal and shove the window forward anyway. Read that back: a stranger's code, injected into my compositor, to undo a behaviour the compositor won't let me switch off.

It should not matter *who* triggered the window coming forward. On Wayland it's the whole ballgame.

### Keyboard input: the OS eats your keys

I bound `Super+.` to an emoji picker. Pressed it. Got nothing — except a stray `e` in my document and a keyboard that ignored every shortcut until I hit Escape.

Took ages to find, because everything *looked* right: extension enabled, keybind correct, no GNOME conflict, nothing in the logs when I pressed the key. That last bit is the tell. Nothing logged because the keypress never *reached* the layer I was staring at.

It was IBus — an input-method framework I didn't even know was running — which had grabbed `Super+.` as its own emoji hotkey by default (thanks, Ubuntu) and was eating the combo *underneath* GNOME's keybinding layer. My key never made it to the thing I'd bound it to. No error. No visible owner. Just a shortcut that vanished into a component I didn't know existed.

One command fixed it, once I knew what to blame. *Finding* what to blame is the tax, and you pay it every single time.

### Window management: write code, or go without

I wanted the Windows trick where you shake a window's title bar to minimise everything else. Tiny thing. Quality of life.

No setting for it. No maintained extension for it on my GNOME version. So the "fix" was to **write a GNOME Shell extension myself** — hook the grab-begin/grab-end signals, poll the pointer, all of it — because the only place you're *allowed* to watch for a window shake on Wayland is inside gnome-shell. You don't get to drive the window manager from a script any more. That door is shut. Inject code into the one process that's allowed to have opinions, or do without.

Then the punchline. Once I'd written it, Wayland wouldn't *load* it without a full logout. No hot reload. No `Alt+F2 → r` to restart the shell in place — that was an X11 thing, and it's gone. So the loop for a trivial window tweak is: write compositor code, log out, log back in, hope. On X11 that was a ten-line script I could run and re-run in a second.

### The GPU picks itself

Hybrid laptop — AMD chip plus an NVIDIA card. Games ran slow. The standard Linux answer is `prime-select nvidia`: tell the system to run on the fast card.

Ran it. Nothing changed. Because `prime-select` only reconfigures the *Xorg* stack, and I'm on Wayland, and Mutter doesn't care what I selected — it picks its "primary" GPU by a hardcoded rule (whichever chip drives the built-in screen, i.e. the slow one) with no override I'm allowed to touch. The tool that exists specifically to let me choose my GPU does nothing, because the thing actually in charge won't take input on the matter.

The clean fix was — say it with me — log into an X11 session, where the tool works exactly as designed.

### Brightness: a knob that used to exist

I wanted to push my screen past its max brightness on a dim panel. On X11 that's `xrandr --brightness 1.3` — a software gamma boost, five seconds, worked for a decade.

On Wayland: no. The tools that would do it lean on a gamma-control protocol that GNOME's Mutter simply doesn't implement. So this one isn't even "hard" or "hidden behind a config file." The capability just isn't there. It existed, I used it, and now it's gone, and there's nothing to configure my way back to.

### And you can't even leave

You've probably spotted the running theme: half these stories end with "so I'd just log into X11." I keep saying it because it kept being the clean answer.

Except I went to actually do it, and I can't. There's no X11 session on this machine to log into — GNOME dropped the Xorg session upstream, and Ubuntu followed. The escape hatch I keep reaching for has been quietly welded shut. So it isn't just that Wayland took the knobs away. It took the *door* too.

## The part that actually stings: it didn't have to be this way

This is where I run out of patience with the whole project.

The headline justification for Wayland is security, and it's a fair one. X11 is a free-for-all — any app can read your keystrokes, screenshot any window, fire fake input anywhere. That's genuinely bad, and Wayland's isolation genuinely fixes it. I'm not arguing the goal.

I'm arguing you didn't have to burn the house down to get there.

Everything Wayland's isolation buys you — apps only seeing their own window, a prompt before something grabs your screen or your input — is a *feature.* It's access control. It could have been added to X11. And whenever I say that, the answer comes back "but Wayland already has it," which… yeah. Duh. That's not a reason it had to be a brand-new protocol. That's just telling me where the feature happens to live today.

Here's the part I only found out later: **X11 already did this, back in 1996.** The X Security Extension — trusted vs untrusted clients, `xauth` untrusted cookies, later XACE. The entire "lock down what an app can see and do" model shipped in the X server decades ago. Nobody used it. Not because it didn't work — because every existing app assumed total access and broke under it, and nobody fancied the unglamorous job of fixing them one at a time.

So the real story isn't "X11 couldn't be secured." It's "securing X11 meant fixing forty years of apps, and starting over was more appealing to the same small team." That's a legitimate engineering call. I might even have made it. But be honest about what it is: a decision that a clean rewrite was easier *for the maintainers*, paid for by *me* losing every scrap of control the old system gave me. Don't hand me a convenience-for-you tradeoff dressed up as a security necessity for me.

That's the bit I can't get past. Not that Wayland is more locked down — that the lockdown was a choice, a working alternative already existed, and the bill for the shortcut got mailed to the users.

## It's not hard. It's that you're not allowed.

There's a gap between **"you shouldn't have to"** and **"you can't,"** and the Wayland crowd smudges it on purpose.

For most people most of the time, the automatic path really is better. Fine. But for the person who knows *exactly* what they want — who's built and rebuilt this same setup across three operating systems — "the automatic behaviour is better, adapt" is the single most infuriating thing you can say. The issue was never that it's difficult. It's **permission.** The override doesn't exist, and they defend it not existing as a feature. Cleaner. Safer. More correct.

And yeah, security and isolation are the usual reasons given, and some of it's real. But it also just works as a flat *no.* "We removed it because it was insecure / messy / unnecessary" reads, from where I'm sitting, identical to "we removed it and you'll cope."

## What they actually mean

Strip the polish off and here's the honest version:

> "Users shouldn't have to do anything" → "Nobody gets to do anything any more."

A principle that only turns up when it's convenient for the people quoting it isn't a principle. It's a slogan stapled to *we decided, deal with it.*

## So what do you do? Not much.

I'm not going to pretend a fix is coming. Some gaps do close over time — protocols get proposals, portals fill holes — but the *attitude* isn't changing, and the specific stuff I want (real input-area control, a shell crash that doesn't nuke my session, scripting my own windows) isn't on any roadmap that helps me this year.

The only thing genuinely in your hands is refusing to get taxed twice. If it's going to break on the next update anyway, at least make putting it back a 30-second job instead of another lost evening:

- **Back up your config.** `dconf dump` your extensions and window settings so a crash or reinstall is one `dconf load`, not a re-tune from memory.
- **Shrink the blast radius.** Run as few shell-injected extensions as you can stand, update the one you can't live without *before* you do OS upgrades, and pick the most actively maintained option so it doesn't rot into a crash next release.
- **Tune at the one layer that still lets you.** libinput won't give you an active area, but its quirks file will at least let you shove the palm/pressure thresholds around. It's not the fix you want. It's the fix you're allowed.

That's the whole thing. Not "Wayland is hard." Wayland decided, for me, that the problem it made for me isn't mine to solve.

I'm not fixing it. I'm managing it.

## Epilogue: the tape drawer

I've now duct-taped a $5,000 laptop. Twice. Two different machines, two different operating systems, two completely unrelated reasons.

The first was a MacBook. The machined aluminium edge was so sharp it left red marks in my wrists, so I ran a strip of tape along the one part of a "perfect" unibody that actually touches a human. The second is this one — taping off the top of the trackpad, because the software that used to let me set a deadzone decided I shouldn't be allowed to have one.

Different company, different OS, different decade. Same story both times: the gear was designed by people so sure they'd nailed it that the only way to make it fit an actual body was two dollars of hardware-store tape — and both times the official line was that *I* was holding it wrong.

The tape isn't the joke. The tape's the receipt.
