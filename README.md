TweetProxy (Symfony / Doctrine / Twitter API)

http://tweetproxy.dev/

popis usera (listing, klik vodi na view usera)

moguće dodati novog usera u katalog samo pristupom kroz URL, npr. http://tweetproxy.dev/username dodaje korisnika "username" u katalog

useri se fetchaju sa APIja i pohranjuju u bazu

http://tweetproxy.dev/username -> single view usera, prikaz usernamea i zadnjih 20 tweetova (20 je u configu), tweetovi se fetchaju sa APIja i pohranjuju u lokalnu bazu

http://tweetproxy.dev/search -> jednostavan search koristeći https://dev.mysql.com/doc/refman/5.6/en/fulltext-search.html
dva polja: query (text input) i user (dropdown sa popisom poznatih usera)

pretražujemo SAMO tweetove iz baze, NE koristimo Twitterov search kroz API
ispisujemo sve tweetove koji matchaju query (ako je upisan) i/ili usera (ako je odabran)
paginacija, 20 itema po stranici (iz configa)
