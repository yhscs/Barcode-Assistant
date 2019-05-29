package red;

import windows.Scanner;
import windows.Setup;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.net.URLConnection;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.security.KeyStore;
import java.security.cert.Certificate;
import java.security.cert.CertificateFactory;
import java.security.cert.PKIXParameters;
import java.security.cert.TrustAnchor;
import java.security.cert.X509Certificate;

import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLHandshakeException;
import javax.net.ssl.TrustManagerFactory;

public class Main {

	public static void main(String[] args) {
	    // try/catch to add yhscs.y115.org certificate to trustst
	    try {
            KeyStore keyStore = KeyStore.getInstance(KeyStore.getDefaultType());
            Path ksPath = Paths.get(System.getProperty("java.home"),
                    "lib", "security", "cacerts");
            keyStore.load(Files.newInputStream(ksPath),
                    "changeit".toCharArray());

            CertificateFactory cf = CertificateFactory.getInstance("X.509");
            try (InputStream caInput = new BufferedInputStream(
                    // this files is shipped with the application
                    Main.class.getResourceAsStream("yhscs.y115.org.cer"))) {
                Certificate crt = cf.generateCertificate(caInput);
                System.out.println("Added Cert for " + ((X509Certificate) crt)
                        .getSubjectDN());

                keyStore.setCertificateEntry("DSTRootCAX3", crt);
            }

            if (false) { // enable to see
                System.out.println("Truststore now trusting: ");
                PKIXParameters params = new PKIXParameters(keyStore);
                params.getTrustAnchors().stream()
                        .map(TrustAnchor::getTrustedCert)
                        .map(X509Certificate::getSubjectDN)
                        .forEach(System.out::println);
                System.out.println();
            }

            TrustManagerFactory tmf = TrustManagerFactory
                    .getInstance(TrustManagerFactory.getDefaultAlgorithm());
            tmf.init(keyStore);
            SSLContext sslContext = SSLContext.getInstance("TLS");
            sslContext.init(null, tmf.getTrustManagers(), null);
            SSLContext.setDefault(sslContext);
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
	    
		Setup s = new Setup();
		while(!s.hasReturned()) {
		    try {
		        Thread.sleep(100);
		    }catch (Exception e){
		        e.printStackTrace();
		    }
		}
		String test  = s.getHash();
		new Scanner(s.getRoomName(),s.getHash());
	}

}
