function cryptN(str,key,encrypt,itr) {
   res="";
   while (str.length>8) {
      res += crypt8(str.substr(0,8),key,encrypt,itr);
      str = str.substr(8);
   }
   if (str.length>0) {
      while (str.length<8) {
     str += ' ';
      }
      res += crypt8(str,key,encrypt,itr);
   }
   while (res.substring(res.length-1,res.length) == ' ') {
      res = res.substring(0,res.length-1);
   }
   return res;
}

//Four-byte truncate
function fbt(x) {
   x = x&0x0FFFFFFFF;
   return x<0?0x0100000000+x:x;
}

//crypt8 en/decrypts a string of length 8
function crypt8(oct,key,encrypt,itr) {
   var y=new Number(0); var z=0; var k=[]; k[0]=k[1]=k[2]=k[3]=0;
   var d=0x9E3779B9; var sum=encrypt?0:d*itr;
   var res="";
   for (var i=0; i<8; ) {
      y=fbt((y<<8)+(oct.charCodeAt(i)&0xFF));
      k[i&3]=fbt((k[i&3]<<8)+key.charCodeAt(i));
      k[i&3]=fbt((k[i&3]<<8)+key.charCodeAt(i+8));
      i++;
      z=fbt((z<<8)+(oct.charCodeAt(i)&0xFF));
      k[i&3]=fbt((k[i&3]<<8)+key.charCodeAt(i));
      k[i&3]=fbt((k[i&3]<<8)+key.charCodeAt(i+8));
      i++;
   }
   if (encrypt) {
      while (itr-->0) {
     y = fbt(y+fbt((z*16)^Math.floor(z/32))+fbt(z^sum)+k[sum&3]);
     sum += d;
     z = fbt(z+fbt((y*16)^Math.floor(y/32))+fbt(y^sum)+k[(sum>>11)&3]);
      }
   } else {
      while (itr-->0) {
     z = fbt(z-fbt(fbt((y*16)^Math.floor(y/32))+fbt(y^sum)+k[(sum>>11)&3]));
     sum -= d;
     y = fbt(y-fbt(fbt((z*16)^Math.floor(z/32))+fbt(z^sum)+k[sum&3]));
      }
   }
   for (var i=4; i-->0; ) {
      res += String.fromCharCode(fbt((y&0xFF000000)>>24));
      y = y<<8;
      res += String.fromCharCode(fbt((z&0xFF000000)>>24));
      z=z<<8;
   }
   return res;
}

function hexify(oct) {
   var res="";
   for (var i=0; i<oct.length; ) {
      var b = oct.charCodeAt(i++);
      res+='0123456789ABCDEF'.charAt(b>>4&0xF);
      res+='0123456789ABCDEF'.charAt(b&0xF);
   }
   return res;
}

function dehexify(hex) {//assumes even number of hex digits
   var res="";
   for (var i=0; i<hex.length; ) {
      var b = hex.charCodeAt(i++);
      var c = hex.charCodeAt(i++);
      res += String.fromCharCode(((b-(b>64?55:48))<<4)+c-(c>64?55:48));
   }
   return res;
}