# ITube

ITube ist ein Skript zur Verabeitung von Video-Dateien, so dass diese leicht
von [Video_XH](https://github.com/cmb69/video_xh) verwendet werden können.
Das Skript ist für die Offline-Verabeitung gedacht, da die direkte Integration
seiner Features in Video_XH praktisch unmöglich ist.

Es besteht natürlich keine *Notwendigkeit* ITube für die Verarbeitung der Videos
zu verwenden; es ist lediglich als bequeme Möglichkeit gedacht für das Web
geeignete Videos vorzubereiten, ähnlich was YouTube, Vimeo, etc. tun, wenn man
Videos auf diese Plattformen hochlädt. Das Hauptproblem, das ITube löst, ist das
Erstellen eines geeigneten Satzes von Video-Dateien, die in unterschiedlichen
Browsern und Umgebungen abgespielt werden können. Z.B. war es noch vor ein paar
Jahren nötig Ogg-Video-Dateien für einige Browser anzubieten, aber heutzutage
sind die Browser dabei die Unterstützung für dieses Containerformat komplett
einzustellen. Schaut man sich detailliert an, welche Codecs tatsächlich unterstützt
werden, wird man mit einer beträchtlichen Komplexität konfrontiert, und will das
alles vielleicht nicht genau verstehen. Daher ist es empfehlenswert die originalen
(möglicherweise nachbearbeiteten) Videos zu behalten, und sie einfach von Zeit zu
Zeit mit neueren Versionen von ITube erneut zu konvertieren, um ein sich immer
weiterentwickelndes Web zu bedienen.

Es ist zu beachten, dass das Erzeugen von Diashows mit Musikuntermalung
ebenfalls unterstützt wird.

## Voraussetzungen

Das Skript läuft unter Windows-Betriebsystemen (oder Emulationsschichten wie
[WineHQ](https://www.winehq.org/)), und benötigt [ffmpeg](https://ffmpeg.org/).

## Download

Das [aktuelle Release](https://github.com/cmb69/video_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

* Das ZIP-Archiv muss irgendwo auf dem Computer entpackt werden.
* Ist [ffmpeg](https://ffmpeg.org/) bereits installiert, ist sicherzustellen,
  das es im `PATH` ist. Andernfalls muss
  [ffmpeg herunter geladen werden](https://www.gyan.dev/ffmpeg/builds/);
  der essenzielle Build ist üblicherweise ausreichend, es sei denn es sollen
  ungewöhnliche Videoformate verarbeitet werden. Dann müssen `ffmpeg.exe`,
  `ffplay.exe` und `ffprobe.exe` in den `PATH`, oder den Ordner, in den das ITube
  ZIP-Archiv entpackt wurde (d.h. direkt neben `itube.bat`), verschoben werden.
* Soll mit Drag & Drop gearbeitet werden, ist es sinnvoll Verknüpfungen zu
  `itube.bat` und `slideshow.bat` auf dem Desktop zu erstellen.

## Einstellungen

Zu Beginn von `itube.bat` gibt es einen Konfigurationsabschnitt, wo ein paar
Parameter angepasst werden können, aber die Voreinstellung sollte gute Ergebnisse
liefern, so dass üblicherweise keine Konfiguration nötig ist.

## Verwendung

`itube.bat` ist ein Kommandozeilenskript, so dass es von der Kommandozeile
aufgerufen werden kann, wenn man mit dieser Arbeitsweise vertraut ist. Z.B.

    itube C:\mein_video.mp4

Alternativ kann die zu verarbeitende Video-Datei per Drag & Drop auf `itube.bat`
(oder eine Verknüfung zu dieser Datei) gezogen werden, was die Verarbeitung
auslöst.

Zunächst erstellt das Skipt einen Unterordner mit dem Basisnamen der Video-Datei
(z.B. für das obige Beispiel `C:\mein_video\`), und alle erzeugten Dateien (ein-
schließlich einiger temporärer Dateien) werden in diesem Ordner abgelegt. Nachdem
das Skript einige grundlegende Parameter ermittelt hat, wird das Video abgespielt,
so dass geprüft werden kann, ob alles in Ordnung ist. Die folgenden Tasten können
verwendet werden, um das Abspielen zu steuern:

| key               | function                         |
|-------------------|----------------------------------|
| q/ESC             | beenden                          |
| p/SPACE           | anhalten                         |
| links/rechts      | 10 Sekunden zurück-/vorspulen    |
| runter/hoch       | 1 Minute zurück-/vorspulen       |
| Seite runter/hoch | 10 Minuten zurück-/vorspulen     |

Nach dem Beenden erfragt das Skript den Zeitpunkt an dem der Screenshot (der als
Poster verwendet wird) aufgenommen werden soll. Der Zeitpunkt zu dem das Video-
Abspielen beendet wurde, ist der Zeile darüber zu entnehmen (Wert ganz links).
Wird dieser Wert eingegeben und mit `ENTER` bestätigt, wird der Screenshot
aufgenommen. Soll das Video erneut abgespielt werden, weil es nicht zur gewünschten
Zeit beendet wurde, muss einfach nur `ENTER` gedrückt werden. Danach fährt das
Skript mit der eigentlichen Video-Verarbeitung fort. Dabei wird der Fortschritt
im Kommandozeilenfenster angezeigt; das kann man sich ansehen, oder auch eine
Pause machen, da die Videoverarbeitung lange dauert. Wird das Kommandozeilenfenster
geschlossen, wird die Verarbeitung abgebrochen.

Nachdem die Verarbeitung beendet ist, sollten einige der erzeugten Dateien
begutachtet werden; ist die Qualität zufriedenstellend, wird der gesamte vom
Skript erzeugte Ordner (einschließlich der Unterordner) auf den Webspace in den
`userfiles/media` Ordner von CMSimple_XH hoch geladen. Dann wird Video_XH genutzt,
um das Video auf der Website anzuzeigen.

### Eingangsvideos

Obgleich ITube beinahe jedes Video verarbeiten wird, das man übergibt, ein paar
Hinweise:

* Es sollten aussagekräftige (aber nicht zu lange) Video-Dateinamen verwendet
  werden; das ist vermutlich gut für SEO, und ebenso für Besucher, die die Videos
  herunterladen.

* Sonderzeichen (wie Leerzeichen und Interpunktion) sind in den Video-Dateinamen
  zu vermeiden; obgleich diese kein Problem für ITube darstellen sollten, können
  diese zu Portabilitätsproblemen führen, und sind generell etwas verwirrend.

* Es sollten ordnungsgemäß nachbearbeitete Videos an ITube übergeben werden.
  Das Skript selbst führt keine Nachbearbeitung durch (außer einer einfachen
  Zeilenentflechtung, falls nötig, und der erforderlichen Verkleinerung), so dass
  je nach Eingangsvideo diese Nachbearbeitung anderweitig durchgeführt werden
  sollte (es gibt anderweitig viele entsprechende kommerzielle und freie Angebote).
  Es wird unbedingt empfohlen die (nachbearbeiteten) Eingangsvideos zu behalten,
  so dass sie später mit neueren Versionen von ITube erneut verarbeitet werden
  können.

* Es sollten Videos mit einer zeitgemäßen Auflösung an ITube übergeben werden.
  Obgleich das Skript sogar 240p Videos akzeptiert, ist es nicht sinnvoll, allen
  Besuchern so kleine Videos zu präsentieren – manche werden zwar nicht in der
  Lage sein größere Videos aufgrund begrenzter Bandbreite oder Gerätebeschränkungen
  anzuschauen,  aber die meisten i.d.R. schon.  Also sollten HD Videos als Eingabe
  verwendet werden, oder zumindest volle PAL/NTSC Auflösung. Sollen dennoch
  kleinere Videos auf der Website angezeigt werden, sollte die Nutzung von
  ausgeklügelten Hochskalierungsalgorthmen in Erwägung gezogen werden (es gibt
  anderweitig sowohl kommerzielle als auch freie Angebote).

* Üblicherweise sollte die Framerate der Videos nicht geändert werden; sowohl
  24fps, 25fps, 30fps, 50fps und 60fps sind geeignet; selbst kleinere FPS-Werte
  können in Ordnung sein. Obgleich es eine Reihe von Möglichkeiten gibt, die
  Framerate während des Nachbearbeitung zu ändern, ist es nicht unwahrscheinlich,
  das dies zu schlechten Ergebnissen führt, wenn die Videos mit ITube verarbeitet
  werden.

* ITube berücksichtigt nur die Haupt-Video- und Audio-Ströme der Eingangsvideos,
  aber ignoriert andere Ströme, da 1 Video- und 1 Audio-Strom der kleinste
  gemeinsame Nenner bezüglich der Browserunterstützung ist. Wenn ITube ungewünschte
  Ströme auswählt, sind die Video vor der Bearbeitung zu re-muxen.

### Diashows

Aus einer Reihe von Bildern kann eine Video-Diashow mit Musikuntermalung erstellt
werden.

Dazu wird ein Ordner mit Bildern im JPEG-Format (`*.jpg`) und eine Audiodatei
(`audio.mp3`, `audio.wav` oder ein anderes `audio.*`) benötigt. Die Abspieldauer
der Audiodatei bestimmt die Abspieldauer des Videos, und jedes Bild wird für den
entsprechenden Bruchteil dieser Zeit angezeigt werden. Gibt es beispielsweise
12 Bilder und die Audio-Abspieldauer ist 1 Minute, wird jedes Bild für 5 Sekunden
angezeigt werden.

Dann muss `slideshow.bat` aufgerufen werden, entweder von der Kommandozeile,
wenn man mit dieser Arbeitsweise vertraut ist, z.B.

    slideshow C:\meine_diashow

Oder die zu verarbeitende Video-Datei wird per Drag & Drop auf `slideshow.bat`
(oder eine Verknüfung zu dieser Datei) gezogen, was die Erstellung des
Diashow-Videos auslöst.

Das Skript ermittelt die geeignetsten Video-Dimensionen, so dass die Bilder
möglichst wenig mit Rändern aufgefüllt werden müssen (etwas Rahmen ist nötig,
wenn Bilder im Hoch- und Querformat gemischt vorliegen). Danach wird das Video,
das die Diashow enthält, erzeugt, und als verlustfreies MKV gespeichert (z.B.
für das obige Beispiel die Datei `C:\meine_diashow\meine_diashow.mkv`).
Das erzeugte Video kann dann angeschaut werden (es ist zu beachten, dass nicht
alle Wiedergabeprogramme in der Lage sind, diese Datei zu spielen; im Zweifel
sollte es der [VLC](https://www.videolan.org/) können). Ist das Video zufrieden
stellend, kann es [mit `itube.bat` konvertiert werden](#verwendung).

## Problembehebung

Programmfehler können auf [Github](https://github.com/cmb69/video_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/) gemeldet werden.
Dort können auch Supportanfragen gestellt werden.

## License

ITube ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von ITube erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
ITube erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

## Credits

ITube nutzt [ffmpeg](https://ffmpeg.org/).
Vielen Dank für die Veröffentlichung dieses mächtigen Multimedia-Frameworks als
Open-Source-Software!

Vielen Dank an die Community im [CMSimple_XH-Forum](https://www.cmsimpleforum.com/)
für Tipps, Vorschläge und das Testen.
Besonders möchte ich *manu* danken, der ein Slideshow-Video vorgestellt hat, das
Auslöser für die Entwicklung von `slideshow.bat` war.
