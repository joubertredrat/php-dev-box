#!/bin/bash
# http://davehope.co.uk/Blog/extract-bandwidth-information-from-lighttpd-log-files/

if [ -z "$1" ]
  then
    echo "No argument supplied, usage: $0 /path/for/access_log"
    exit -1
fi

cat $1 | awk '{
month=substr($4,5,3)
year= substr($4,9,4)
timstamp=year" "month
bytes[timstamp] += $10
} END {
for (date in bytes)
printf("%s %20d MB\n", date, bytes[date]/(1024*1024))
}' | sort -k1n -k2M
