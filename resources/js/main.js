jQuery.fn.insertAt = function(index, element) {
    var lastIndex = this.children().length;
    if (index < 0) {
      index = Math.max(0, lastIndex + 1 + index);
    }
    this.append(element);
    if (index < lastIndex) {
      this.children().eq(index).before(this.children().last());
    }
    return this;
}


function currentClientTime($langs){
  var time = new Date(),
  h = time.getHours(), // 0-24 format
  m = time.getMinutes();
  d = time.getDay();
  D = time.getDate();
  Y = time.getFullYear();
  M = time.getMonth();
  
  return $langs['days'][d] + ', ' + $langs['months'][M] + ' ' + D + ', ' + Y + ' ' + h + ':'+m;
}