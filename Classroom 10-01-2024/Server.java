import java.util.Scanner;
import java.io.BufferedReader;
import java.io.BufferedOutputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.ServerSocket;
import java.net.Socket;

public class Server {
    public static void main(String[] args) throws IOException {
        Scanner input = new Scanner(System.in);

        System.out.print("Inserisci porta: ");
        int port = input.nextInt();

        ServerSocket server = new ServerSocket((port > 0 && port < 65535) ? port : 1234);
        System.out.println("Il server sta ascoltanto sulla porta " + server.getLocalPort());

        input.close();

        String elenco = """
        Servizi disponibili:
            1) Somma
            2) Sottrazione
            3) Moltiplicazione
            4) Divisione
            exit) Esci
        """;

        while (true) {
            Socket client = server.accept();
            new Thread(() -> {
                try {
                    BufferedReader daClient = new BufferedReader(new InputStreamReader(client.getInputStream()));
                    DataOutputStream versoClient = new DataOutputStream(new BufferedOutputStream(client.getOutputStream()));

                    versoClient.writeBytes(elenco + "\r\n");
                    versoClient.flush();

                    String message = daClient.readLine();
                    int message_int;
                    double a=0, b=0;

                    while (!message.toLowerCase().equals("exit")) {
                        System.out.println("Il client con IP " + client.getInetAddress() + " invia: " + message);

                        try {
                            message_int = Integer.parseInt(message);
                        } catch (Exception e) {
                            versoClient.writeBytes("Comando non riconosciuto\n" + elenco + "\r\n");
                            versoClient.flush();
                            message = daClient.readLine();
                            continue;
                        }

                        if (message_int >= 1 && message_int <= 4) {
                            try {
                                versoClient.writeBytes("Inserisci il primo numero\r\n");
                                versoClient.flush();
                                a = Double.parseDouble(daClient.readLine());

                                versoClient.writeBytes("Inserisci il secondo numero\r\n");
                                versoClient.flush();
                                b = Double.parseDouble(daClient.readLine());
                            } catch (Exception e) {
                                versoClient.writeBytes("Errore nell'inserimento dei numeri\n" + elenco + "\r\n");
                                versoClient.flush();
                                message = daClient.readLine();
                                continue;
                            }
                        }

                        switch(message_int) {
                            case 1:
                                versoClient.writeBytes("Il risultato della somma è: " + (a + b) + "\n" + elenco + "\r\n");
                                versoClient.flush();
                                break;
                            case 2:
                                versoClient.writeBytes("Il risultato della sottrazione è: " + (a - b) + "\n" + elenco + "\r\n");
                                versoClient.flush();
                                break;
                            case 3:
                                versoClient.writeBytes("Il risultato della moltiplicazione è: " + (a * b) + "\n" + elenco + "\r\n");
                                versoClient.flush();
                                break;
                            case 4:
                                versoClient.writeBytes("Il risultato della divisione è: " + (a / b) + "\n" + elenco + "\r\n");
                                versoClient.flush();
                                break;
                            default:
                                versoClient.writeBytes("Comando non riconosciuto\n" + elenco + "\r\n");
                                versoClient.flush();
                        }

                        message = daClient.readLine();
                    }
                    
                    versoClient.close();
                    client.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }).start();
        }
    }
}