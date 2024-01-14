import base64
import json
import os
import functions_framework
from google.cloud import storage

#this function merges all of the dictionaries located in jack-anagram-base, and then finds all true anagrams, then sorting them alphabetically
@functions_framework.http
def merge_dictionaries(request):

    #create a storage client
    storage_client = storage.Client()
    #list anagram dictionary names
    blobs = storage_client.list_blobs("jack-anagram-base")
    
    #the dictionary of all anagram dictionaries combined
    overall_dictionary = {}

    #for every dictionary in jack-anagram-base
    for anagram_dictionary in blobs:
        #open the current dictionary
        storage_client = storage.Client()
        bucket = storage_client.bucket("jack-anagram-base")
        dic_to_read = bucket.blob(anagram_dictionary.name)
        with dic_to_read.open("r", encoding='latin-1') as f:
            dic_to_add = json.loads(f.read())
            #for every anagram key in the current dictionary
            for key in dic_to_add:
                #if the anagram already exists in overall dictionary:
                if key in overall_dictionary:
                    #for each word to the anagram code in the new dictionary were checking
                    for word in dic_to_add[key]:
                        #print (word)
                        #check if the word is already in the large dictionary, if not then add it
                        #print (dic_recipient[key])
                        if word not in overall_dictionary[key]:
                            overall_dictionary[key].append(word)
                            #print (overall_dictionary[key])
                #otherwise add it as a new key to the overall dictionary
                else:
                    overall_dictionary[key] = dic_to_add[key]


    #remove non anagrams from the dictionary
    revised_dictionary = {}
    for key in overall_dictionary:
        #if there exists more one word for an anagram key (its an actual anagram)
        if len(overall_dictionary[key]) > 1:
            revised_dictionary[key] = overall_dictionary[key]

    #convert dictionary to alphabetically sorted lines for a txt file
    jackText = ""
    #get a sorted list of the dictionary keys
    sortedKeys = sorted(revised_dictionary.keys())
    for curKey in sortedKeys:
        jackText += curKey + ": "
        #also sort the words from each anagram key
        for word in sorted(revised_dictionary[curKey]):
            jackText += word + ", "
        jackText += "\n"

    #upload jack text (the result file) to the result bucket as a txt
    bucket_name = "jack-result-base"
    contents = (jackText)
    destination_blob_name = ("anagram_results.txt")

    #reconfigure storage client to send data
    bucket = storage_client.bucket(bucket_name)
    blob = bucket.blob(destination_blob_name)

    blob.upload_from_string(contents)


    return ("success")
