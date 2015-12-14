package windows;

import java.awt.Container;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Toolkit;
import java.awt.image.BufferedImage;
import java.io.IOException;

import javax.imageio.ImageIO;
import javax.swing.ImageIcon;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.SwingConstants;

import gfx.ScaledImageLabel;
import util.Keyboard;

@SuppressWarnings("serial")
public class Scanner extends JFrame{	
	
	private String roomName;
	private String hash;
	
	public Scanner(String roomName, String hash) {
		super("Scanner Utility");
        this.roomName = roomName;
        this.hash = hash;
		Toolkit tk = Toolkit.getDefaultToolkit();
		int xSize = ((int) tk.getScreenSize().getWidth());
		int ySize = ((int) tk.getScreenSize().getHeight());
		this.setPreferredSize(new Dimension(640,480));
		this.setSize(xSize,ySize);
		this.setExtendedState(JFrame.MAXIMIZED_BOTH); 
        this.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        addComponentsToPane(this.getContentPane());
        this.pack();
        this.setVisible(true);
        this.addKeyListener(new Keyboard());
	}
	
	public void addComponentsToPane(Container p) {
		p.setLayout(new GridBagLayout());
	    GridBagConstraints c = new GridBagConstraints();
	    c.fill = GridBagConstraints.HORIZONTAL;
	    
		JLabel label = new JLabel("                ",SwingConstants.CENTER);
		c.fill = GridBagConstraints.HORIZONTAL;
		c.gridheight = 1;
		c.gridx = 0;
	    c.gridy = 0;
	    p.add(label, c);
	    
	    label = new JLabel(label.getText(),SwingConstants.CENTER);
		c.fill = GridBagConstraints.HORIZONTAL;
		c.gridheight = 1;
		c.gridx = 2;
	    c.gridy = 0;
	    p.add(label, c);
	    
		label = new JLabel("<html><center>" + roomName + " sign in/out station</center></html>",SwingConstants.CENTER);
		Font labelFont = label.getFont();
		label.setFont(new Font(labelFont.getName(), Font.PLAIN, 86));
		c.fill = GridBagConstraints.HORIZONTAL;
		c.gridheight = 1;
		c.gridx = 1;
	    c.gridy = 0;
	    p.add(label, c);
	    
		label = new JLabel("<html><center>Please use the barcode scanner and your Student ID to sign in and out of this room.</center></html>",SwingConstants.CENTER);
		labelFont = label.getFont();
		label.setFont(new Font(labelFont.getName(), Font.PLAIN, 40));
		c.fill = GridBagConstraints.HORIZONTAL;
		c.gridheight = 1;
		c.gridx = 1;
	    c.gridy = 1;
	    p.add(label, c);
	    
	    BufferedImage image;
		try {
			image = ImageIO.read(getClass().getResourceAsStream("/pictures/scan.png"));
			ScaledImageLabel imageLabel = new ScaledImageLabel();
			imageLabel.setIcon(new ImageIcon(image));
			c.fill = GridBagConstraints.BOTH;
		    c.weightx = 1.0;
		    c.weighty = 1.0;
		    c.gridx = 1;
		    c.gridy = 2;
		    p.add(imageLabel, c);
		} catch (IOException e) {
			e.printStackTrace();
			JLabel imageLabel = new JLabel("Cannot open file!");
			c.fill = GridBagConstraints.BOTH;
		    c.gridx = 1;
		    c.gridy = 2;
		    p.add(imageLabel, c);
		}
	}
}
