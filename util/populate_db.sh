#!/usr/bin/env bash 

db=$1
collection=$2

cmd="mongo "$db" --eval db."$collection".find().count()"
echo $cmd
$cmd

cmd="mongo "$db" --eval db.dropDatabase()"
echo $cmd
$cmd

cmd="mongoimport --file "$db"_"$collection.json" -d "$db" -c "$collection""
echo $cmd
$cmd
