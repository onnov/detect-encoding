# Detect encoding

Text encoding definition class based on a range of code page character numbers.

So far, in PHP v7. * The mb_detect_encoding function does not work well.
Therefore, you have to somehow solve this problem.
This class is one solution.


Built-in Encodings and accuracy:

letters ->   | 5     | 15    | 30    | 60    | 120   | 180   | 270
---          |   --- |  ---  | ---   |---    |---    |---    |---
windows-1251 | 99.13 | 98.83 | 98.54 | 99.04 | 99.73 | 99.93 | 100.0
koi8-r       | 99.89 | 99.98 | 100.0 | 100.0 | 100.0 | 100.0 | 100.0
iso-8859-5   | 81.79 | 99.27 | 99.98 | 100.0 | 100.0 | 100.0 | 100.0
ibm866       | 99.81 | 99.99 | 100.0 | 100.0 | 100.0 | 100.0 | 100.0
mac-cyrillic | 12.79 | 47.49 | 73.48 | 92.15 | 99.30 | 99.94 | 100.0 

Worst accuracy with mac-cyrillic, you need at least 60 characters to determine this encoding with an accuracy of 92.15%. Windows-1251 encoding also has very poor accuracy. This is because the numbers of their characters in the tables overlap very much.

Fortunately, mac-cyrillic and ibm866 encodings are not used to encode web pages. By default, they are disabled in the script, but you can enable them if necessary.


letters ->       | 5     | 10    | 15    | 30    | 60    |
---              |   --- |  ---  | ---   |---    |---    |
windows-1251     | 99.40 | 99.69 | 99.86 | 99.97 | 100.0 |
koi8-r           | 99.89 | 99.98 | 99.98 | 100.0 | 100.0 |
iso-8859-5       | 81.79 | 96.41 | 99.27 | 99.98 | 100.0 |

The accuracy of the determination is high even in short sentences from 5 to 10 letters. And for phrases from 60 letters, the accuracy of determination reaches 100%.


Determining the encoding is very fast, for example, text longer than 1,300,000 Cyrillic characters is checked in 0.00096 sec. (on my computer)



Link to the idea:

http://patttern.blogspot.com/2012/07/php-python.html
