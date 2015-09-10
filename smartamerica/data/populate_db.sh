#!/usr/bin/env bash 

db=magi
declare -a collections=("rlserrors_u" "rlserrors_a" "pronyerrors_u" "pronyerrors_a" "pktcounter_u" "pktcounter_a" "pktcounter_du" "pktcounter_da")

for collection in "${collections[@]}"
do
    cmd="mongoimport --file "$db"_"$collection.json" -d "$db" -c "$collection""
    echo $cmd 
    $cmd
done        
