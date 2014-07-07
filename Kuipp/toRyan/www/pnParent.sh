#!/bin/bash
#
# takes one argument, start, stop, restart

if test "$1" == ""; then
    echo "pnParent.sh requires one argument: start, stop, or restart"
    exit 0
fi

if test "$1" == "stop" -o "$1" == "restart"; then
    if test -z $(pgrep pnChild.sh); then
	echo "no pnChild.sh to stop, skipping"
    elif [ -z $(pkill pnChild.sh) ]; then
	echo "pnChild.sh stopped"
    fi
fi

if test "$1" == "start" -o "$1" == "restart"; then
    if test -z $(pgrep pnChild.sh); then
	echo "pnChild.sh started"
	/opt/third-party/apple/pnChild.sh &
	disown -h
    else
	echo "pnChild.sh is already running!"
    fi
fi

exit 0