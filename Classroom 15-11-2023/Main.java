import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.*;
import java.util.Arrays;
import java.util.Scanner;


public class Main {
    public static void main(String args[]) throws UnknownHostException, IOException {
        Scanner input = new Scanner(System.in);

        System.out.print("Inserisci url: ");
        String url = input.nextLine();

        input.close();

        String[] cleaned = url.split("//")[1].split("/");
        String route = String.join("/", Arrays.copyOfRange(cleaned, 1, cleaned.length));

        Socket client = new Socket(cleaned[0], 80);

        DataOutputStream versoServer = new DataOutputStream(new BufferedOutputStream(client.getOutputStream()));
        BufferedReader daServer = new BufferedReader(new InputStreamReader(client.getInputStream()));

        versoServer.writeBytes("GET /" + route + " HTTP/1.0\nHost: " + cleaned[0] + "\n\n");
        versoServer.flush();

        String stringaRicevutaDalServer;

        while ((stringaRicevutaDalServer = daServer.readLine()) != null)
            System.out.println(stringaRicevutaDalServer);

        client.close();
    }
}