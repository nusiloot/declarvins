curl -s $1 | grep '"id":' | sed 's/.*"id":"//' | sed 's/".*//' |sed 's/ /%20/g' | while read OBJ; do
	OBJREV=$(curl -s http://localhost:5984/declarvin/$OBJ | sed 's/{"_id":"//' | sed 's/","_rev":"/?rev=/' | sed 's/".*//'|sed 's/ /%20/g')
	echo $OBJREV
	curl -s -X DELETE http://localhost:5984/declarvin/$OBJREV
done
