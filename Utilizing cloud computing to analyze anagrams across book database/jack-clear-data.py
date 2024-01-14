import functions_framework

# Imports the Google Cloud client library
from google.cloud import storage
#this is a function which removes all files from jack-anagram-base and jack-result base before starting a new anagram generation
@functions_framework.http
def remove_files(request):

    #create a storage client
    storage_client = storage.Client()
    #remove all blobs in anagram base
    blobs = storage_client.list_blobs("jack-anagram-base")

    for blob in blobs:
        blob.delete()
    #remove all blobs in result base
    blobs = storage_client.list_blobs("jack-result-base")

    for blob in blobs:
        blob.delete()
    return ("success")
