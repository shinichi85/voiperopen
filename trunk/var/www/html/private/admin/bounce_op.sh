#!/bin/bash

if pgrep op_server.pl > /dev/null 2>&1
then
	killall -9 op_server.pl > /dev/null 2>&1
fi
