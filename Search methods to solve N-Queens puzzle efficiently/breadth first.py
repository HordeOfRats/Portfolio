import ast
import copy
import bisect
import time


boardSize = int(input("Board size:"))
start_state = [0]*boardSize
#print(start_state)
#see if number is in a sorted list using bisection
def checkForDupe(a, x):
    #Locate the leftmost value exactly equal to x
    i = bisect.bisect_left(a, x)
    if len(a)!= i and a[i] == x:
        return True
    #if we dont find one, its not in the list
    return False

#function to shift a given queen up
def shiftQueen(state, queenPos):
    newstate = copy.deepcopy(state)
    #shift up
    if(newstate[queenPos] != (len(newstate)-1)):
        newstate[queenPos] = newstate[queenPos] + 1
        return newstate
    
#convert state to unique x-ary representation
def convertState(state):
    #print(state)
    multiplier = len(state)
    representation = 0
    exponent = 0
    for x in range(len(state)):
        representation = representation + (state[x]*(multiplier**exponent))
        exponent = exponent + 1
    return int(representation)


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
        




#generate a solution using our functions
def breadthGenerateSolution(start_start):
    start_time = time.time()
    reachedStates = []
    bisect.insort(reachedStates, convertState(start_state))
    #list of states to expand
    frontier = []
    frontier.append(start_state)
    success = False
    i = 0
    while(frontier and (success == False)):
        i = i + 1
        #remove the front state from frontier and make it current state
        currentState = frontier.pop(0)
        #print(currentState)
        #print(reachedStates)
        #if (i % 1000 == 0):
        #    print(i)
        #    print(len(reachedStates))
        #check for valid states reachable from current state breadth first
        for x in range(len(currentState)):
            #if moving queen up in given position is valid
            #print(currentState[x])
            #print(len(currentState))
            if (currentState[x] < len(currentState)-1):
                #create a temp state to represent this shift
                tempState = shiftQueen(currentState, x)
                #if its not in the the reachedStates list, add to frontier and reachedStates
                tempconvert = convertState(tempState)
                if (checkForDupe(reachedStates, tempconvert) == False):
                    bisect.insort(reachedStates, tempconvert)
                    frontier.append(tempState)
                    #check if this state is a solution
                    if(getHeuristic(tempState) == 1):
                        solution = tempState
                        success = True

    if (success == False):
        print("No solution found")
    elif (success == True):
        print("reached solution:", solution)
        print("iterations of while loop:", i)
        end_time = time.time()
        print("Time elapsed:",(end_time-start_time))
        return((end_time-start_time),i,)
breadthGenerateSolution(start_state)
#print(getHeuristic([6,1,3,1,5,0,2,4]))
#print(convertState([0,2,1,3]))
#testing time and iterations
avgTime = 0
avgIter = 0
tests = 3
for test in range(tests):
    timeAdd, iterAdd = breadthGenerateSolution(start_state)
    avgTime = avgTime + timeAdd
    avgIter = avgIter + iterAdd
print("avg time for runs:", (avgTime/tests))
print("average while loop iterations:",(avgIter/tests))
        