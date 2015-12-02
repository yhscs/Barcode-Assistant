package red;

import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;

public class Keyboard implements KeyListener {
	String que = "";
	
    /** Handle the key typed event from the text field. */
    public void keyTyped(KeyEvent e) {
        que += e.getKeyChar();
        if(que.length() > 1 && que.substring(que.length()-1, que.length()).equals("\n")) {
        	que = que.substring(0,que.length() - 1);
        	System.out.print(que);
        	if(que.length() == 7) {
        		try {
        			Integer.parseInt(que);
            		System.out.println("... Accepted.");
            		DataObject data = new DataObject(que, "null");
            		System.out.println(data);
        		} catch (NumberFormatException err) {
        			System.out.println("... Declined. Not real numbers.");
        		}
        	} else {
        		System.out.println("... Declined. Not 7 characters.");
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
}

