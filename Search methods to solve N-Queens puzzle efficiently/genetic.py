import ast
import copy
import bisect
import random
import numpy as np
import time
import random

#settings
#starting population size as int
startingPopulationSize = 25
#percentage (out of 100%) for gene mutation
mutationChance = 45
#input for board size
boardSize = int(input("Board size:"))

#print(start_state)


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
        
def generateStartingPopulation(boardSize, startingPopulation):
    population = []
    #do x times to generate a starting population of size x
    for x in range(startingPopulation):
        gene = []
        #generate a element of population by repeatedly appending queens
        for y in range(boardSize):
            gene.append(random.randint(0,boardSize-1))
        population.append(gene)
    return population

#function used to generate the offspring of two parents, given a cutoff point
def generateOffspring(parent1, parent2, geneCutoff):
    offspring1 = (parent1[:geneCutoff]+parent2[geneCutoff:])
    offspring2 = (parent2[:geneCutoff]+parent1[geneCutoff:])
    return offspring1, offspring2

#function to mutate a child gene, according to mutation chance
def mutate(gene, mutationChance):
    #roll a number between 1 and 100 to see if this gene should mutate
    if (random.randint(1,100) > (100 - mutationChance)):
        #mutate the gene randomly
        gene[random.randint(0,len(gene)-1)] = random.randint(0,len(gene)-1)
    return gene



#generate a solution using our functions
def generateGeneticSolution(boardSize, startingPopulationSize, mutationChance):
    start_time = time.time()
    finished = False
    success = False
    i = 0
    population = generateStartingPopulation(boardSize, startingPopulationSize)
    #print(population)
    #check if by chance any of the starting population satisfies the puzzle
    while (finished == False):
        i = i + 1
        newPopulation = []
        #if (i % 1000 == 0):
        #    print(i)
        #get fitness values for current population
        fitnessValues = [getHeuristic(chromosome) for chromosome in population]
        #print("fitness values:",fitnessValues)
        #check if there is a gene with fitness 1 in population
        if (1 in fitnessValues):
            for gene in population:
                if (getHeuristic(gene) == 1):
                    success=True
                    finished=True
                    solution = gene
        #if not, generate new genes using genetic algorithm
        else:  
            #get the sum of all fitness values
            fitnessSum = (sum(fitnessValues))
            #print(fitnessSum)
            #https://rocreguant.com/roulette-wheel-selection-python/2019/
            #convert the fitness values into percentages for roulette wheel using minimization
            #convert to minimization rather than maximization, so that a lower fitness(better) is more likely to pass on
            fitnessValues = [(fitnessSum/gene) for gene in fitnessValues]
            fitnessSum = (sum(fitnessValues))
            fitnessValues = [(gene/fitnessSum) for gene in fitnessValues]
            #generate enough new genes for a new generation of equal size to the starting population
            #get gene cutoff point
            geneCutoff = random.randint(1, boardSize-2)
            genelabels = list(range(0,len(population)))
            #npPopulation = np.asarray(population)
            for x in range(0, startingPopulationSize, 2):
                #get new parents randomly using roulette wheel
                parents = np.random.choice(genelabels, size=2, replace=False, p=fitnessValues)
                #parents = random.choices(genelabels, fitnessValues, k=2)
                offspring1, offspring2 = generateOffspring(population[parents[0]], population[parents[1]], geneCutoff)
                newPopulation.append(offspring1)
                newPopulation.append(offspring2)
            #perform mutation upon the new generation
            population = [mutate(gene,mutationChance) for gene in newPopulation]
    if (success == False):
        print("No solution found")
    elif (success == True):
        print("reached solution:", solution)
        print("iterations of while loop:", i)
        end_time = time.time()
        print("Time elapsed:",(end_time-start_time))
        return((end_time-start_time),i)

generateGeneticSolution(boardSize, startingPopulationSize, mutationChance)
#print(generateStartingPopulation(5,4))
#print(generateOffspring([3,2,4,5,6,4],[1,2,1,3,1,2], 4))
#testing time and iterations
