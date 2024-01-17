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

        while (true) {
            Socket client = server.accept();
            new Thread(() -> {
                try {
                    BufferedReader daClient = new BufferedReader(new InputStreamReader(client.getInputStream()));
                    DataOutputStream versoClient = new DataOutputStream(new BufferedOutputStream(client.getOutputStream()));

                    String message = daClient.readLine();

                    while (!message.toLowerCase().equals("exit")) {
                        System.out.println("Il client con IP " + client.getInetAddress() + " invia: " + message);

                        versoClient.writeBytes("CONTINUARE\r\n");
                        versoClient.flush();

                        message = daClient.readLine();
                    }
                    
                    versoClient.writeBytes("Bye bye" + "\r\n");
                    versoClient.flush();

                    client.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }).start();
        }
    }
}