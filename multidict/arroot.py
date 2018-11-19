import sys
from nltk.stem.isri import ISRIStemmer
arstemmer = ISRIStemmer()
token = sys.argv[1]
root = arstemmer.stem(token)
print (root)
