package util;

import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;

import net.Network;
import windows.Scanner;

public class Keyboard implements KeyListener {
	private String room;
	private String roomHash;
	private Scanner scanner;
	
	public Keyboard(Scanner s, String room, String roomHash) {
		this.room = room;
		this.roomHash = roomHash;
		this.scanner = s;
	}
	
	String que = "";
	
    /** Handle the key typed event from the text field. */
    public void keyTyped(KeyEvent e) {
    	if(e.getKeyChar() == KeyEvent.VK_BACK_SPACE) {
    		if(que.length() > 0) {
    			que = que.substring(0,que.length() - 1);
    		}
    	} else {
    		if(que.length() < 7 || e.getKeyChar() == '\n') {
    			que += e.getKeyChar();
    		}
    	}
    	scanner.setSubtitleText(que);
        if(que.length() > 1 && que.substring(que.length()-1, que.length()).equals("\n")) {
        	que = que.substring(0,que.length() - 1);
        	System.out.print(que);
        	if(que.length() == 7) {
        		try {
        			Integer.parseInt(que);
            		System.out.println("... Accepted.");
            		try {
	        			int status = Network.putData(room, roomHash, que);
	        			if(status == Network.CHECKIN) {
	        				scanner.setImage("/pictures/ok.png");	
	        				scanner.setHeaderText("Welcome!");
	        				scanner.setSubtitleText("Make sure you scan your Student ID again when you leave!");	
	        			} else if (status == Network.CHECKOUT) {
	        				scanner.setImage("/pictures/exit.png");	
	        				scanner.setHeaderText("Goodbye!");
	        				scanner.setSubtitleText("Thanks for signing out! See you soon!");	
	        			}
	        		} catch (Exception ohno) {
	        			System.out.println("Something happened!");
	    				scanner.setImage("/pictures/no.png");	
	        			scanner.setHeaderText("Error:");
	        			scanner.setSubtitleText(ohno.getMessage());
	        		}
        		} catch (NumberFormatException err) {
        			System.out.println("... Declined. Not real numbers.");
                	scanner.setHeaderText("Oh no! You tried to break something :(");
        			scanner.setSubtitleText("The thing you scanned had letters. A student ID does not contain letters.");
        			scanner.setImage("/pictures/no.png");	
        		}
        	} else {
        		System.out.println("... Declined. Not 7 characters.");
            	scanner.setHeaderText("Oh no! You tried to break something :(");
    			scanner.setSubtitleText("The thing you scanned was either too long or too short.");
    			scanner.setImage("/pictures/no.png");	
        	}
            que = "";
        }
    }

    /** Handle the key-pressed event from the text field. */
    public void keyPressed(KeyEvent e) {
    }

    /** Handle the key-released event from the text field. */
    public void keyReleased(KeyEvent e) {
    }

	public void clearQue() {
		que = "";
	}
}

