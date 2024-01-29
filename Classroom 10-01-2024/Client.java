import java.util.Scanner;
import java.net.UnknownHostException;
import java.io.IOException;
import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.InputStreamReader;
import java.net.Socket;

public class Client {
    public static void main(String[] args) throws UnknownHostException, IOException {

        Scanner input = new Scanner(System.in);

        boolean connesso = false;
        Socket client = null;
        while (!connesso) {
            System.out.print("Inserisci url del server: ");
            String url = input.nextLine();

            if (url.equals("")) {
                System.out.println("L'url non può essere vuoto\n");
                continue;
            }

            String[] cleaned = (url.contains("//")) ? url.split("//")[1].split("/")[0].split(":") : url.split("/")[0].split(":");
            
            try {
                client = new Socket(cleaned[0], cleaned.length == 2 ? Integer.parseInt(cleaned[1]) : 80);
                System.out.println("Connesso al server " + client.getInetAddress() + " sulla porta " + client.getPort());
                connesso = true;
            } catch (Exception e) {
                System.out.println("Errore nella connessione al server, riprova.\n");
            }
        }

        Socket finalClient = client;
        BufferedReader daServer = new BufferedReader(new InputStreamReader(finalClient.getInputStream()));
        DataOutputStream versoServer = new DataOutputStream(new BufferedOutputStream(finalClient.getOutputStream()));

        String message = "";
        String server_message = "";

        while(true) {
            while(daServer.ready()) {
                server_message = daServer.readLine();
                System.out.println("server: " + server_message);
            }

            System.out.print("Inserisci un messaggio: ");
            message = input.nextLine();
            if (message.equals("")) {
                System.out.println("Il messaggio non può essere vuoto");
                continue;
            }

            versoServer.writeBytes(message + "\r\n");
            versoServer.flush();

            server_message = daServer.readLine();
            if (server_message == null) {
                System.out.println("Il server ha chiuso la connessione");
                break;
            }

            System.out.println("\nserver: " + server_message);
        }

        client.close();
        input.close();
    }
}