from audioop import rms
from math import sqrt
import math
import random
from tkinter import Y
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
from numba import jit
df = pd.read_excel(r'C:\Users\Thick\Documents\Programming\year 2\ai 2\setUse.xlsx')

df_tr = df.iloc[:876,:]
df_va = df.iloc[876:1168,:]
df_te = df.iloc[1168:,:]

epocsToDo= 3000
learningRate = 0.01
stepSize = 0.2
hiddenNodes = 8
momentum = 0.9

#create arrarys for mins and maxes of columns for standardization
stanMaxs = np.array([])
stanMins = np.array([])
testStanMaxs = np.array([])
testStanMins = np.array([])

#create a seperate array representing merged training and validation data
df_trva = df.iloc[:1168,:]

#calculate mins and maxes for both the training and validation set, and the testing set.
for x in range(0, len(df_trva.columns), 1):
    stanMaxs =  np.append(stanMaxs ,(df_trva.iloc[:, x].max()))
    stanMins = np.append(stanMins ,(df_trva.iloc[:, x].min()))

for x in range(0, len(df_te.columns), 1):
    testStanMaxs = np.append(testStanMaxs ,(df_te.iloc[:, x].max()))
    testStanMins = np.append(testStanMins ,(df_te.iloc[:, x].min()))

#print(testStanMaxs)
#print(testStanMins)

#functions to standardize data
def standardizeTest(dataToStan):
    for x in range(0, len(dataToStan.columns), 1):
        for y in range(0, len(dataToStan), 1):
            dataToStan.iat[y, x] = 0.8*(((dataToStan.iat[y, x])-(testStanMins[x]))/(testStanMaxs[x]-testStanMins[x]))+0.1

def standardize(dataToStan):
    for x in range(0, len(dataToStan.columns), 1):
        for y in range(0, len(dataToStan), 1):
            dataToStan.iat[y, x] = 0.8*(((dataToStan.iat[y, x])-(stanMins[x]))/(stanMaxs[x]-stanMins[x]))+0.1

@jit
def deStanOutput(toDeStan):
    toDeStan = (((toDeStan - 0.1) / 0.8)*(stanMaxs[inputs]-stanMins[inputs]))+stanMins[inputs]
    return toDeStan

@jit
def testDeStanOutput(toDeStan):
    toDeStan = (((toDeStan - 0.1) / 0.8)*(testStanMaxs[inputs]-testStanMins[inputs]))+testStanMins[inputs]
    return toDeStan

standardize(df_tr)
standardize(df_va)
standardizeTest(df_te)

inputs = len(df_tr.columns)-1

#functions to generate random weights and biases
def randomiseWeights():
    weights = np.zeros(hiddenNodes*(inputs+1))
    for x in range(0, (hiddenNodes*(inputs + 1)), 1):
        weightToAdd = random.uniform(0.01, 2/inputs)
        if (random.randint(0,1)) == 1:
            weightToAdd = weightToAdd * -1
        weights[x] = weightToAdd
    return weights

def randomiseBiases():
    biases = np.zeros(hiddenNodes+1)
    for x in range(0, hiddenNodes+1, 1):
        biasToAdd = random.uniform(0.01, 2/inputs)
        if (random.randint(0,1)) == 1:
            biasToAdd = biasToAdd * -1
        biases[x] = biasToAdd
    return (biases)




#function to apply activation function
@jit(nopython=True)
def applyActivation(toActivate):
    toActivate = 1/(1+((math.e)**(-toActivate)))
    return (toActivate)

#print(applyActivation(0.07831284964585869))

weights = randomiseWeights()
biases = randomiseBiases()
#print (weights)
#print (len(weights))
#print(biases)
#print (len(biases))
#len(df_tr)

df_tr = df_tr.to_numpy()
df_va = df_va.to_numpy()
df_te = df_te.to_numpy()

rowUValues = np.zeros(hiddenNodes+1)
rowDValues = np.zeros(hiddenNodes+1)
epochTeRMSEValues = np.zeros(epocsToDo)
epochVaRMSEValues = np.zeros(epocsToDo)
epochModelled = np.zeros(len(df_tr))
epochObserved = np.zeros(len(df_tr))



#use the number of epocs selected to cycle through x times
@jit(nopython=True)
def generateANN(rowUValues, rowDValues, epochTeRMSEValues, epochVaRMSEValues,epocsToDo, df_tr, df_va,df_te, weights, biases, stepSize, hiddenNodes, inputs, momentum):
    lastMSE = 0
    oldWeights = np.empty_like(weights)
    oldWeights[:] = weights
    oldBiases = np.empty_like(biases)
    oldBiases[:] = biases
    for curEpoch in range(1, epocsToDo+1, 1):
        #cycle through the dataset once
        for row in range(0, len(df_tr), 1):
            previousWeightChange = np.zeros(len(weights))
            previousBiasesChange = np.zeros(len(biases))
            #clear row values (no longer used)
            #rowUValues = np.array([])
            #rowDValues = np.array([])
            #calculate activated values of hidden nodes
            for node in range(0, hiddenNodes, 1):
                S = 0
                for column in range(0, inputs, 1):
                    S = S + (df_tr[row, column])*weights[(node*inputs)+column]
                S = S +(1 * biases[node])
                U = applyActivation(S)
                rowUValues[node] = U
            S = 0
            #calculate activated value of output node
            for toOutput in range(0, hiddenNodes, 1):
                S = S + (rowUValues[toOutput])*weights[(hiddenNodes*inputs)+toOutput]
            S = S +(1 * biases[hiddenNodes])
            U = applyActivation(S)
            #calculate weight decay
            omega = 0
            for weight in range(0, len(weights), 1):
                omega = omega + ((weights[weight])**2)
            for bias in range(0, len(biases), 1):
                omega = omega + ((biases[bias])**2)
            omega = omega * (1 / (2 * (len(biases)+len(weights))))
            reg = (1 / (stepSize * curEpoch))
            #calculate delta value of output
            outputD = (((df_tr[row, inputs])-U) + (omega * reg))*(U*(1-U))
            #calculate delta values of hidden nodes 
            for node in range(0, hiddenNodes, 1):
                nodeD = ((weights[(hiddenNodes*inputs)+node])*outputD)*((rowUValues[node])*(1-(rowUValues[node])))
                rowDValues[node] = nodeD
            #change the weights to and biases of hidden nodes using delta and activated values
            for node in range(0, hiddenNodes, 1):
                currentInput = 0
                for toNode in range(node*inputs, ((node*inputs)+inputs), 1):
                    if row == 0:
                        oldWeight = weights[toNode]
                        weights[toNode] = weights[toNode] + (stepSize * (rowDValues[node]) * (df_tr[row, currentInput]))
                        previousWeightChange[toNode] = weights[toNode] - oldWeight
                        currentInput = currentInput + 1
                    else:
                        oldWeight = weights[toNode]
                        weights[toNode] = weights[toNode] + (stepSize * (rowDValues[node]) * (df_tr[row, currentInput])) + (momentum * previousWeightChange[toNode])
                        previousWeightChange[toNode] = weights[toNode] - oldWeight
                        currentInput = currentInput + 1
                if row == 0:
                    oldBias = biases[node]
                    biases[node] = biases[node] + (stepSize * (rowDValues[node]) * 1)
                    previousBiasesChange[node] = biases[node] - oldBias
                else:
                    oldBias = biases[node]
                    biases[node] = biases[node] + (stepSize * (rowDValues[node]) * 1) + (momentum * previousBiasesChange[node]) 
                    previousBiasesChange[node] = biases[node] - oldBias
            #change the weights to and bias of the output node using delta and activated values
            currentInput = 0
            for toNode in range((inputs*hiddenNodes), (inputs*hiddenNodes) + hiddenNodes, 1):
                weights[toNode] = weights[toNode] + (stepSize * (outputD) * rowUValues[currentInput])
                if row == 0:
                    oldWeight = weights[toNode]
                    weights[toNode] = weights[toNode] + (stepSize * (outputD) * rowUValues[currentInput])
                    previousWeightChange[toNode] = weights[toNode] + (stepSize * (outputD) * rowUValues[currentInput]) - oldWeight
                    currentInput = currentInput + 1
                else:
                    oldWeight = weights[toNode]
                    weights[toNode] = weights[toNode] + (stepSize * (outputD) * rowUValues[currentInput]) + (momentum * previousWeightChange[toNode])
                    previousWeightChange[toNode] = weights[toNode] + (stepSize * (outputD) * rowUValues[currentInput]) - oldWeight
                    currentInput = currentInput + 1
            if row == 0:
                oldBias = biases[hiddenNodes]
                biases[hiddenNodes] = biases[hiddenNodes] + (stepSize * outputD * 1)
                previousBiasesChange[hiddenNodes] = biases[hiddenNodes] - oldBias
            else:
                oldBias = biases[hiddenNodes]
                biases[hiddenNodes] = biases[hiddenNodes] + (stepSize * outputD * 1) + (momentum * previousBiasesChange[hiddenNodes])
                previousBiasesChange[hiddenNodes] = biases[hiddenNodes] - oldBias
        #calculate rmse on testing data for epoch
        rmse = 0
        for row in range(0, len(df_te), 1):
            #calculate activated values of hidden nodes
            for node in range(0, hiddenNodes, 1):
                S = 0
                for column in range(0, inputs, 1):
                    S = S + (df_te[row, column])*weights[(node*inputs)+column]
                S = S +(1 * biases[node])
                U = applyActivation(S)
                rowUValues[node] = U
            S = 0
            #calculate activated value of output node
            for toOutput in range(0, hiddenNodes, 1):
                S = S + (rowUValues[toOutput])*weights[(hiddenNodes*inputs)+toOutput]
            S = S +(1 * biases[hiddenNodes])
            U = applyActivation(S)
            uDeStan = testDeStanOutput(U)
            rmse = rmse + (testDeStanOutput(df_te[row, inputs])-uDeStan)**2
        rmse = rmse / len(df_te)
        rmse = sqrt(rmse)
        testRMSE = rmse
        epochTeRMSEValues[curEpoch-1] = rmse

        #calculate rmse on validation data for epoch
        mse = 0
        for row in range(0, len(df_va), 1):
            #calculate activated values of hidden nodes
            for node in range(0, hiddenNodes, 1):
                S = 0
                for column in range(0, inputs, 1):
                    S = S + (df_va[row, column])*weights[(node*inputs)+column]
                S = S +(1 * biases[node])
                U = applyActivation(S)
                rowUValues[node] = U
            S = 0
            #calculate activated value of output node
            for toOutput in range(0, hiddenNodes, 1):
                S = S + (rowUValues[toOutput])*weights[(hiddenNodes*inputs)+toOutput]
            S = S +(1 * biases[hiddenNodes])
            U = applyActivation(S)
            uDeStan = deStanOutput(U)
            mse = mse + (deStanOutput(df_va[row, inputs])-uDeStan)**2
        mse = mse / len(df_va)
        rmse = sqrt(rmse)
        epochVaRMSEValues[curEpoch-1] = rmse
        #print (rmse)
        #bold driver implementation
        if (curEpoch % 10) == 0 :
            if lastMSE == 0:
                lastMSE = mse
                #print ("here")
            else:
                if (lastMSE * 1.04) < mse:
                    weights[:] = oldWeights
                    biases[:] = oldBiases
                    #print ("repeated 10 epochs")
                    stepSize = stepSize * 0.7
                    if stepSize < 0.01:
                        stepSize = 0.01
                else:
                    stepSize = stepSize * 1.05
                    oldWeights[:] = weights
                    oldBiases[:] = biases
                    lastMSE = mse
                    if stepSize > 0.5:
                        stepSize = 0.5
                    #print ("no im here")





        print (curEpoch)
        #print (testRMSE)
        #print (weights)
generateANN(rowUValues, rowDValues, epochTeRMSEValues, epochVaRMSEValues,epocsToDo, df_tr, df_va,df_te, weights, biases, stepSize, hiddenNodes, inputs, momentum)
#print (biases)
#print (weights)
#print (epochRMSEValues)
# plotting the rmse
toPlot = "mvo"
if toPlot == "RMSE":
    plt.plot(epochTeRMSEValues, 'r')
    plt.plot(epochVaRMSEValues, 'b')
    plt.xlabel("Epochs")
    plt.ylabel("RMSE")
    plt.show()
elif toPlot == "mvo":
    for row in range(0, len(df_te), 1):
        #calculate activated values of hidden nodes
        for node in range(0, hiddenNodes, 1):
            S = 0
            for column in range(0, inputs, 1):
                S = S + (df_te[row, column])*weights[(node*inputs)+column]
            S = S +(1 * biases[node])
            U = applyActivation(S)
            rowUValues[node] = U
        S = 0
        #calculate activated value of output node
        for toOutput in range(0, hiddenNodes, 1):
            S = S + (rowUValues[toOutput])*weights[(hiddenNodes*inputs)+toOutput]
        S = S +(1 * biases[hiddenNodes])
        U = applyActivation(S)
        epochModelled[row] = testDeStanOutput(U)
        epochObserved[row] = testDeStanOutput(df_te[row, inputs])
        #epochModelled[row] = U
        #epochObserved[row] = (df_tr[row, inputs])
        #print (epochModelled)
        #print (epochObserved)
        


    plt.scatter(epochObserved, epochModelled)
    a, b = np.polyfit(epochObserved, epochModelled, 1)
    plt.plot(epochObserved, a*epochObserved+b) 
    plt.axis('equal')
    plt.ylim(ymin=0)
    plt.xlabel("Observed")
    plt.ylabel("Modelled")
    plt.show()

        
        


