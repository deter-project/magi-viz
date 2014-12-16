#!/usr/bin/env python

from pymongo import MongoClient
import json

conn = MongoClient()
col = conn['dnsmitm']['experiment_data']

col.remove({'agent' : 'monitor_agent'})

def insertMonitorData(time, host, packets):
	col.insert({"created": time, "packets": packets, "trafficDirection": "out", "agent": "monitor_agent", "host": host })

for time in range(0,20):
	insertMonitorData(time, "auth", 8)
	insertMonitorData(time, "attacker", 0)

for time in range(20,40):
	insertMonitorData(time, "auth", 0)
	insertMonitorData(time, "attacker", 8)
	
for time in range(40,60):
	insertMonitorData(time, "auth", 8)
	insertMonitorData(time, "attacker", 0)


#AAL
col.remove({'agent' : 'aal'})
eventItr = 0

def insertAalEvent(streamName, eventType, eventLabel, timeStamp):
    global eventItr
    col.insert({'agent' : 'aal',
                'streamName' : streamName,
                'eventItr' : eventItr,
                'eventType' : eventType,
                'eventLabel' : eventLabel,
                'created' : timeStamp})
    eventItr += 1
    
insertAalEvent('mainStream', 'streamInit', 'mainStream', 0.1)

insertAalEvent('mainStream', 'event', 'monitor_agent -> startCollection', 0.2)

insertAalEvent('mainStream', 'event', 'ping_agent -> startPing', 0.3)
insertAalEvent('mainStream', 'trigger', 'Wait for 20 seconds', 0.4)
#insertAalEvent('mainStream', 'triggerComplete', 'Done: 20 seconds', 20.1)

insertAalEvent('mainStream', 'event', 'attack_agent -> startAttack', 20.2)
insertAalEvent('mainStream', 'trigger', 'Wait for 20 seconds', 20.3)
#insertAalEvent('mainStream', 'triggerComplete', 'Done: 20 seconds', 40.1)

insertAalEvent('mainStream', 'event', 'defense_agent -> startDefense', 40.2)
insertAalEvent('mainStream', 'trigger', 'Wait for 20 seconds', 40.3)
#insertAalEvent('mainStream', 'triggerComplete', 'Done: 20 seconds', 60.1)

insertAalEvent('mainStream', 'exit', 'Exit', 60.2)



#ORCH
col.remove({'agent' : 'orchestrator'})

eventItr = 0

def insertOrchEvent(streamName, eventType, eventLabel, timeStamp):
    global eventItr
    col.insert({'agent' : 'orchestrator',
                'streamName' : streamName,
                'eventItr' : eventItr,
                'eventType' : eventType,
                'eventLabel' : eventLabel,
                'created' : timeStamp})
    eventItr += 1
    
insertOrchEvent('mainStream', 'streamInit', 'mainStream', 0.1)

insertOrchEvent('mainStream', 'event', 'monitor_agent -> startCollection', 0.2)

insertOrchEvent('mainStream', 'event', 'ping_agent -> startPing', 0.3)
insertOrchEvent('mainStream', 'trigger', 'Wait for 20 seconds', 0.4)
insertOrchEvent('mainStream', 'triggerComplete', 'Done: 20 seconds', 19.1)

insertOrchEvent('mainStream', 'event', 'attack_agent -> startAttack', 19.2)
insertOrchEvent('mainStream', 'trigger', 'Wait for 20 seconds', 19.3)
insertOrchEvent('mainStream', 'triggerComplete', 'Done: 20 seconds', 39.1)

insertOrchEvent('mainStream', 'event', 'defense_agent -> startDefense', 39.2)
insertOrchEvent('mainStream', 'trigger', 'Wait for 20 seconds', 39.3)
insertOrchEvent('mainStream', 'triggerComplete', 'Done: 20 seconds', 59.1)

insertOrchEvent('mainStream', 'exit', 'Exit', 59.2)


#TOPOLOGY
col.remove({'agent' : 'topo_agent'})
    
nodes = ['client', 'cache', 'cloud', 'auth', 'attacker']
edges = [('client', 'cache'), 
         ('cache', 'cloud'), 
         ('cloud', 'auth'), 
         ('cloud', 'attacker')]

col.insert({'agent':'topo_agent', 
            'nodes':json.dumps(nodes), 
            'edges':json.dumps(edges)})
