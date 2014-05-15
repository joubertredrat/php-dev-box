#!/bin/bash
# http://ubuntuforums.org/showthread.php?t=885344
# du-sort : Provides a sorted du in human readable format

# MAXDEPTH :  set to zero to show all
MAXDIR=1

# SORT ORDER  (true/false)
ASCENDING=true

# DEFAULT ARGUMENTS
ARGS='-xh'

# mktmp file
DATA=$(mktemp)

# ADD MAX-DEPTH ARG
if [ "$MAXDIR" -gt "0" ]
	then
	ARGS="$ARGS --max-depth=$MAXDIR"
fi

# CHECK FOR EXTRA ARGUMENT
if ! [ -z $1 ]
then
	ARGS="$ARGS $1"
fi

# RUN DU
du $ARGS >$DATA 2>/dev/null

# SORT DATA AND OUTPUT
if $ASCENDING
then
	for P in K M G
	do
		grep -e "[0-9]$P" $DATA|sort -n
	done

else
	for P in G M K
	do
		grep -e "[0-9]$P" $DATA|sort -nr
	done
fi

#remove datafile
rm $DATA