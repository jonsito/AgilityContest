/*
 Simple implementation for stack and fifo queues in javascript
 from https://stackoverflow.com/questions/1590247/how-do-you-implement-a-stack-and-a-queue-in-javascript
 */

class Stack {
    constructor(...items){
        this._items = []
        if(items.length>0) items.forEach(item => this._items.push(item) )
    }
    push(...items){ //push item to the stack
        items.forEach(item => this._items.push(item) )
        return this._items;
    }
    pop(count=0){ //pull out the topmost item (last item) from stack
        if(count===0) return this._items.pop()
        else return this._items.splice( -count, count )
    }
    peek(){ // see what's the last item in stack
        return this._items[this._items.length-1]
    }
    size(){ //no. of items in stack
        return this._items.length
    }
    isEmpty(){ // return whether the stack is empty or not
        return this._items.length===0
    }
    toArray(){
        return this._items;
    }
}

class Queue{
    constructor(...items){ //initialize the items in queue
        this._items = []
        // enqueuing the items passed to the constructor
        this.enqueue(...items)
    }
    enqueue(...items){ //push items into the queue
        items.forEach( item => this._items.push(item) )
        return this._items;
    }
    dequeue(count=1){ //pull out the first item from the queue
        this._items.splice(0,count);
        return this._items;
    }
    peek(){ //peek at the first item from the queue
        return this._items[0]
    }
    size(){ //get the length of queue
        return this._items.length
    }
    isEmpty(){ //find whether the queue is empty or no
        return this._items.length===0
    }
}