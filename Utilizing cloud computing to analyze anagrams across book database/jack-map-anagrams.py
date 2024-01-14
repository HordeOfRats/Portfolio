import base64
import json
import os
import functions_framework
from google.cloud import storage

#mapper funtion which gets the anagrams in a single book, this function is done in parallel in the workflow
@functions_framework.http
def find_anagrams(request):

    #get stop words from stop words bucket
    storage_client = storage.Client()
    bucket = storage_client.bucket("jack-stop-words")
    book = bucket.blob("stop_words.txt")
    #get the stop words as a list
    with book.open("r", encoding='latin-1') as f:
            stop_words = f.read().split(",")
         
    #get the name of the book to read from the json request
    request_json = request.get_json()

    bucket_name = "jack_book_base"
    book_name = request_json['toRead']
         
    #open the book requested
    storage_client = storage.Client()
    bucket = storage_client.bucket(bucket_name)
    book = bucket.blob(book_name)

    #empty dictionary of anagrams which we will be adding to
    anagrams = {}

    with book.open("r", encoding='latin-1') as f:
        book_to_filter = (f.read()).lower()
        #remove stop words before grammar by splitting the book into a list of strings
        book_to_filter = book_to_filter.split()
        for word in book_to_filter:
            #if the word is a stop word, then replace it with nothing
            if word in stop_words:
                word = ''
        #re merge the book into a single string
        book_to_filter = ' '.join(book_to_filter)

        #remove stop symbols
        #stop symbols which might need a space
        for stop_symbol in ["!", ":", ".", "?", "*", "/", '"', "(", ")", ";", ",", "-"]:
            book_to_filter = book_to_filter.replace(stop_symbol, ' ')
        #for stop symbols should be replaced with no space
        for stop_symbol in ["'"]:
            book_to_filter = book_to_filter.replace(stop_symbol, '')

        #split the book into a list of strings again
        book_splitted = book_to_filter.split()
             
        #find anagrams by going through every word in the splitted book
        for word in book_splitted:
            #remove words which contain numbers 
            #dont allow single letter words
            #catch any missed stop words
            if len(word) > 1 and word.isalpha() and word not in stop_words:
                #get the anagram key of the word. eg aaadegntv for advantage
                sorted_word = ''.join(sorted(word))
                #if the anagram key isnt already in our anagram dictionary, add it and the word, otherwise add to the list at the approprate anagram key
                if sorted_word not in anagrams:
                    anagrams[sorted_word] = [word]
                elif word not in anagrams[sorted_word]:
                    anagrams[sorted_word].append(word)



    #upload the dictionary as a txt file to jack-anagram-base
    bucket_name = "jack-anagram-base"
    contents = (json.dumps(anagrams, ensure_ascii=False))
    destination_blob_name = ("ana_"+book_name)

    #reconfigure storage client to send data
    bucket = storage_client.bucket(bucket_name)
    blob = bucket.blob(destination_blob_name)

    blob.upload_from_string(contents)


    return ("success")