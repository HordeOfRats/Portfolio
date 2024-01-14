import ast
import copy
import bisect
import random
import numpy as np
import time
import random

#settings
#board size
boardSize = int(input("Board size:"))



#heuristic function, used here to check if a state is a solution. if it is, its heuristic will be 1
def getHeuristic(state):
    #summative heuristic score
    heuristicSum = 1
    #we do not need to check for vertical matches due to how we generate states making it impossible for there to be 2 queens on the same column
    #check horizontally for matches
    for x in range(len(state)):
        if (state.count(x) > 1):
            heuristicSum = heuristicSum + (state.count(x)-1)
    #check right up diagonals
    diagonalOccurences = 0
    for c in range(-len(state)+1, len(state)-1):
        for x in range(0,len(state)):
            #y = mx + c
            if ((state[x])== x+c ):
                diagonalOccurences = diagonalOccurences + 1
        if (diagonalOccurences > 1):
            heuristicSum = heuristicSum + (diagonalOccurences-1)
        diagonalOccurences = 0
    #check right down diagonals
    for c in range(-len(state)+1, len(state)-1):
        for x in range(0,len(state)):
            #y = x + c
            #remember to account that we want the diagonal to crosss the board, so add board size to RHS
            if ((state[x])== (-x)+c+(len(state))):
                diagonalOccurences = diagonalOccurences + 1
        if (diagonalOccurences > 1):
            heuristicSum = heuristicSum + (diagonalOccurences-1)
        diagonalOccurences = 0
    return heuristicSum
        
#function to randomise an array in place
def randomise(state):
    #generate a new value for each element of the state
    for x in range(len(state)):
        #get a value between 0 and the length of the state minus 1
        state[x] = (random.randint(0,(len(state))-1))
    return


#generate a solution using our functions
def randomGenerateSolution(boardSize):
    start_time = time.time()
    finished = False
    success = False
    i = 0
    currentState = [0]*boardSize
    while (finished == False):
        i = i + 1
        #if (i % 1000 == 0):
        #    print(i)
        randomise(currentState)

        #check if the current state satisfies the puzzle. if so stop searching
        if(getHeuristic(currentState) == 1):
            success = True
            finished = True

    if (success == False):
        print("No solution found")
    elif (success == True):
        print("reached solution:", currentState)
        print("iterations of while loop:", i)
        end_time = time.time()
        print("Time elapsed:",(end_time-start_time))
        return((end_time-start_time),i,)

#randomGenerateSolution(boardSize)
#testing time and iterations
avgTime = 0
avgIter = 0
tests = 10
for test in range(tests):
    timeAdd, iterAdd = randomGenerateSolution(boardSize)
    avgTime = avgTime + timeAdd
    avgIter = avgIter + iterAdd
print("avg time for runs:", (avgTime/tests))
print("average while loop iterations:",(avgIter/tests))