#!/usr/bin/env bash 

db=magi
declare -a collections=("pronyerrors_u" "pronyerrors_a" "pktcounter_du" "pktcounter_da")

for collection in "${collections[@]}"
do
    cmd="mongoimport --file "$db"_"$collection.json" -d "$db" -c "$collection""
    echo $cmd 
    $cmd
done        
