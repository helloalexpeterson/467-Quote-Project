function sortTable(sortType, colIndex, tableID){

// Set ColIndex and compFunc depending on Col Header
var colIndex;
var compFunc;

switch(sortType){
  case "string":
    compFunc = (x, y, asc) => {
        if (x.toLowerCase() == y.toLowerCase()) return false;

        let cmp = ( x.toLowerCase() > y.toLowerCase() );
        if (asc)
            return cmp;
        else 
            return !cmp;
    };
    break;

  case "number":
    compFunc = (x, y, asc) => {
        if (x.toLowerCase() == y.toLowerCase()) return false;

        let cmp = Number(x) > Number(y);
        if (asc)
            return cmp;
        else 
            return !cmp;
    };
    break;

//   case "numAscend":
//   compFunc = (x, y) => {
//       return Number(x) > Number(y);
//   };
//   break;
  
    case "lname":
    compFunc = (x, y, asc) => {

/*
    var compFunc = (x, y) => {
        const xnames = x.split(" ");
        const ynames = y.split(" ");

        return (xnames[xnames.length - 1].toLowerCase() > ynames[ynames.length - 1].toLowerCase());
    };
    break;
*/

      const xnames = x.split(" ");
      const ynames = y.split(" ");

      let xlname = xnames[xnames.length - 1];
      let ylname = ynames[ynames.length - 1];

      if (xlname.toLowerCase() == ylname.toLowerCase()) return false;
      
      let cmp = (xlname.toLowerCase() > ylname.toLowerCase());
      if (asc)
        return cmp;
      else
        return !cmp;
    };
    break;

  default:    // Error
    colIndex = -1;
    return colIndex;
}

// Taken from W3 Schools https://www.w3schools.com/howto/howto_js_sort_table.asp
/* Make a loop that will continue until
no switching has been done: */

var table = document.getElementById(tableID);
var rows = table.rows;

var asc = true, switchcount = 0;
var switching = true;

while (switching) {
  // Start by saying: no switching is done:
  switching = false;
  rows = table.rows;

  var i;
  /* Loop through all table rows (except the
  first, which contains table headers): */
  for (i = 1; i < (rows.length - 1); i++) {
    // Start by saying there should be no switching:
    shouldSwitch = false;
    /* Get the two elements you want to compare,
    one from current row and one from the next: */
    var x = rows[i].getElementsByTagName("TD")[colIndex].innerHTML;
    var y = rows[i + 1].getElementsByTagName("TD")[colIndex].innerHTML;

    // Check if the two rows should switch place:
    if (compFunc(x, y, asc)) {
      // If so, mark as a switch and break the loop:
      shouldSwitch = true;
      break;
    }
  }
  if (shouldSwitch) {
    /* If a switch has been marked, make the switch
    and mark that a switch has been done: */
    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
    switching = true;
    // Each time a switch is done, increase this count by 1:
    switchcount ++;
  } else {
    /* If no switching has been done AND the direction is "asc",
    set the direction to "desc" and run the while loop again. */
    if (switchcount == 0 && asc) {
      asc = !asc;
      switching = true;
    }
  }
}
}