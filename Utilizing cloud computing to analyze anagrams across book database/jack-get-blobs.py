import functions_framework
from flask import jsonify

# Imports the Google Cloud client library
from google.cloud import storage
#function used to get the names of all books in the input bucket
@functions_framework.http
def search_for_books(request):

    #create a storage client
    storage_client = storage.Client()
    #list all books in the bucket
    books = storage_client.list_blobs("jack_book_base")

    #list of book names
    book_list = []
    for book in books:
        book_list.append(book.name)
    #return as json to workflow
    book_list = jsonify(book_list)
    return(book_list)
