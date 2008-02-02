Joss
####

© Jan Javorek, 2007 (http://work.javorek.net/joss)

Chceš-li více informací, navštiv stránky autora: http://www.javorek.net/.


Co je Joss?
***********

Joss je jednoduchý webový program, díky němuž si můžeš velmi snadno tvořit a
spravovat své vlastní stránky. Přehnaně inteligentní lidé tomu říkají framework,
RS (redakční systém) nebo CMS (content management system, systém pro správu
obsahu).

Joss si vyhrazuje právo všem okolo tykat. Věří, že když bude k druhým co nejvíce
přátelský, bude mu opláceno stejně. Chtěl by být opravdu jednoduchým a rychlým
redakčním systémem. Zároveň se nebojí nejnovějších technologií a doufá, že se mu
podaří je zkrotit tak, aby byly prospěšné a implementovány správně.

Kód je napsán v objektovém PHP 5 a využívá nebo hodlá využívat co nejpoctivěji
technologií XML, XHTML, CSS a JavaScript.

Díky všem, kteří umožnili vznik Joss vydáním svých skvělých projektů jako Open
Source. Především to byli génius David Grudl a můj přítel Michal Wiglasz.
Projekt těží z toho, že využívá kód formátovače Texy! a některých částí
frameworku Nette.


Licence
*******

Joss je svobodný program. Můžeš ho zdarma stáhnout, libovolně upravovat a
používat ve svých aplikacích v souladu s podmínkami GNU General Public License
v2. Přečti si pozorně licenční podmínky v souboru license.cs.txt.


Požadavky
*********

Nutné:
- PHP 5 a vyšší
- možnost zapisovat do některých adresářů
- možnost využívat soubory .htaccess a běžná nastavení serveru

Vhodné:
- povolená direktiva allow_url_fopen v php.ini serveru
- mod_rewrite


Struktura
*********

Joss má ve své základní podobě několik složek a souborů. V následujících
odstavcích vysvětlím jejich význam.

Upozorňění: Všechny soubory jsou v UTF-8. Joss nedává na výběr.

cache
=====

Tento adresář je pro ukládání souborů s cache (keš). Díky ní lze stránky
poskytovat mnohem rychleji.

class
=====

Složka s jádrem Jossu. Zde jsou všechny PHP třídy, které program využívá.
Obsahuje také třídu texy.php, což je (pokud možno) poslední verze Texy!
formátovače.

config
======

Složka s konfigurací. Zde je jeden velmi důležitý soubor -- ini.php. Je to tzv.
ini soubor s velmi jednoduchým zápisem svého obsahu. Je maskován jako PHP
skript, aby nebyl čitelný nezvanými hosty zvenčí. V tomto souboru jsou všechna
potřebná nastavení pro vytvoření webu s Jossem. Více o konfiguraci Jossu je v
odstavcích o instalaci.

content
=======

Nejdůležitější složka pro webmastera. Nachází se zde zdrojové soubory stránek
v Texy! syntaxi. Obsahuje 3 nutné podsložky foot, head a text.

Když se tvoří HTML stránka webu, poskládá se z hlavičky, obsahu a patičky. Běžně
jsou hlavička a patička neměnné a podle zadané adresy se pouze mění vlastní
obsah stránky. Tak to dělá i Joss -- skládá stránku z výchozí hlavičky
head/_main.texy, textu podle toho co se nachází v adrese a výchozí patičky
foot/_main.texy. Texty čerpá z adresáře text/ a zobrazuje vždy ten odpovídající
adrese -- například www.example.com/krasny-letni-den zavolá
text/krasny-letni-den.texy. Pokud v adrese nic není, zavolá se soubor
text/index.texy.

Navíc existují speciální soubory pro chyby -- jedná se o _404.texy, _403.texy a
_500.texy pro odpovídající chybové kódy HTTP (např. 404 -- stránka nenalezena).
Tyto soubory se také nacházejí v adresáři s texty.

Pokud chceš nastavit pro jednu stránku jinou hlavičku (resp. patičku) než pro
zbytek webu, jednoduše do patřičného adresáře pridáš soubor s názvem stránky,
které patří. Např. budu mít v head/_main.texy hlavičku pro celý web, ale na
úvodní stránce budu chtít hlavičku jinou, speciální, tak přidám soubor
head/index.texy a Joss se s tím popere.

Aby toho nebylo málo, Joss umí pracovat i s takzvanými linky. Link je soubor
pojmenovaný nazev-stranky.link a jediné co obsahuje je text s adresou na jiný
zdrojový soubor v Texy!. Může být relativní i absolutní -- lze takto vložit do
svého webu .texy soubor třeba přes adresu
http://www.example.com/stranka.texy. Linky lze použít pro všechny druhy
zdrojových souborů -- hlavičky, patičky i texty.

Speciální složkou v content může být adresář nazvaný rss. Do něj patří linky
na soubory s RSS. Joss tento adresář automaticky prohledá a pokud v něm najde
odkazy na RSS, připojí je ke každé stránce webu. Odkazů může být klidně více
a název souboru zde nehraje jinou roli než rozlišovací. Vytvořím-li např.
feed.link a vložím do něj řádek http://www.example.com/feed/rss.xml, připojí
se automaticky k webu. Nutno podotknout, že zpracovávání linků na RSS je velmi
náročné a bez cachování nepoužitelné.


doc
===

Adresář s dokumentací, licencí apod.


file
====

Složka se soubory k webu. Jedná se např. o dokumenty PDF nebo obrázky aj.
věci, které se např. nabízejí ke stažení. Soubory download.php a random.php
jsou využívány pro možnost vynutit stažení souboru (i když je to např. obrázek)
nebo zobrazit náhodný obrázek. Více o možném použití je v sekci o rozšíření
syntaxe.


modules
=======

Složka s moduly Jossu. Každá třída zde je potomkem třídy Module a definuje
nějaké rozšíření syntaxe Texy!. V úvodním komentáři některých složitějších
rozšíření může být vysvětleno pořadí a typ předávaných paramterů. Pokud si
napíšeš vlastní modul, stačí vycházet z nějakého existujícího, trochu
prostudovat původní třídu Module a výsledek svého snažení uložit do tohoto
adresáře. Třída MTřída pak bude automaticky použitelná jako {{třída: p1, ...}}.


tpl
===

Adresář se soubory týkajícími se vzhledu webu. Ve složce /css/ jsou vyhledávány
CSS soubory, v /js/ JavaScript. CSS pro celý web je _main.css, pokud ale Joss
najde soubor odpovídající názvem požadované stránce, vloží ten namísto _main.
Pokud bys chtěl udělat pouze rozšíření hlavního stylu, využij funkce CSS
include a hlavní styl na začátku vlož do speciálního.

Pokud je nalezen styl s příponou .ie.css místo .css, je vložen s podmíněným
komentářem pro Internet Explorer za běžný styl. Pro název IE stylu jinak platí
stejná pravidla.

Navíc, styly jež nesou název _media.css, kde media je jeden ze způsobů použití
stylu vzhledem k zobrazovacímu zařízení podle specifikace CSS, je vložen do
každé stránky webu s patřičnými podmínkami. Např. _print.css bude vložen do
každé stránky jako styl pro tisk.

Do složky tpl je vhodné také ukládat obrázky aj. soubory související s designem
webu. Jejich uložení/pojmenování nemá žádné speciální regule -- sobory jsou
využity ve tvém CSS a záleží na tobě, jak je pojmenuješ a jak uspořádáš.


Instalace
*********

Nakopíruj všechny soubory a složky tak jak jsou do místa, kde má být web.
Poté, ještě před tím, než poprvé zobrazíš jakoukoliv stránku v prohlížeči,
uprav soubor config/ini.php.

Určitě není dobré to dělat to v opačném pořadí. Vlastně je to zcela nežádoucí.

- htaccess: je nastavení, jestli smí Joss používat .htaccess
- modRewrite: totéž pro mod_rewrite, bez něj vypadají adresy jako
  index.php?doc=stránka
- debug: nesahat, nechat false
- title: název celého webu (lze vynechat)
- language: majoritní jazyk webu (prázdné = angličtina), zkratka podle
  http://www.w3.org/WAI/ER/IG/ert/iso639.htm
- compressed: komprimuje html výstup, může být rychlejší, ale zdrojový kód
  je nečitelný (nemusí být na škodu)
- cached: vypíná/zapíná keš, silně doporučeno nechat zapnuto
- xhtml: přepíná mezi XHTML výstupem (true) a HTML 4.0 (false)
- allowRobots: politika vůči vyhledávačům (false jim zakáže přístup na web)
- author: autor webu do hlavičky (nedůležité)
- copyriht: copyright webu do hlavičky (nedůležité)
- keywords: klíčová slova webu pro vyhledávače, oddelená čárkou (bezvýznamné)
- description: popis webu jednou/dvěma větami (důležité pro vyhledávače)

Po nastavení je možné nechat web zobrazit. Vše by se mělo automaticky nastavit.
Je možné, že Joss napíše, aby se změnila zápisová práva pro některé adresáře.
To lze dobře pomocí chmod.


Rozšíření syntaxe
*****************

odkazy
======

Odkaz například "takovýto":index vede automaticky na stránku index.texy tvého
webu. Odkaz na "soubor":file:obrazek.png vytvoří odkaz na obrazek.png ve složce
file. Odkaz "obrazek":download:obrazek.png navíc vynutí jeho stažení.

expression
==========
Vyhodnotí výraz v PHP a vrátí jeho hodnotu. Dostane-li v parametru např.
`2000-5`, vrátí číslo 1995.

glossary
========
Něco na způsob mapy webu. Vypíše seznam všech stránek na webu, v abecedním
pořadí podle jejich URL. Seznam je vypsán jako HTML seznam DIR.

head
====

S minimálními parametry vypíše kompletní hlavičku HTML dokumentu. Vše si najde
sám a zjistí sám. Parametry nejsou povinné, výchozí se berou z ini.php. Jejich
nastavením však lze měnit hlavičku podle potřeb aktuální stránky oproti celému
zbytku webu. Vykreslí vše od DOCTYPE po uzavírací tag </head>. Pokud najde
v kořenovém adresáři webu soubor favicon.ico, připojí ho také. Více informací
v sekci o složce tpl.

include
=======

Následuje předaný odkaz a Texy! soubor, na který směřuje, vloží a zpracuje na
místo, kde je volán. Bez cachování může být tato operace docela náročná. Určitě
není dobré vkládat mnoho stránek kaskádovitě do sebe.

menu
====

Vytvoří běžný odkaz na zadanou stránku, ale obalí ho tagem <li></li> a tomu
navíc přidá třídu class="active", pokud stránka, na niž odkaz směřuje, je
přávě prohlíženou stránkou.

server
======

Poskytuje hodnoty z proměnné $_SERVER.

title
=====

Vloží nadpis právě zobrazované stránky (lze samozřejmě použít např. v hlavičkách
a patičkách, které běžně o aktuální stránce nemají potuchy).

