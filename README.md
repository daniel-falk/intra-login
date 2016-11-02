# intra-login
Easy to use login module for your private pages or intranet. Written in PHP.

# Coding style:

## Functions
A function is defined with return type and name at the same row (makes content search easier).
Starting bracket is placed on its own line.
Example:
'''
int foo(int arg1)
{
    ..
}
'''

Functions with many arguments can be broken to new lines
'''
int foo2(int arg1,
        int arg2,
        int arg3)
{
    ...
}
'''

## Conditional statements
Conditional statements should not have brackets if only one line of code. If either one of the statement alternatives have multiple rows then both can have brackets. Example:
'''
if (myVar == true){
    ...
} else {
    ...
    ...
    ...
}
''''
