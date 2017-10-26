package windows;

import java.awt.Color;
import java.awt.Container;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Toolkit;
import java.awt.image.BufferedImage;
import java.io.IOException;
import java.util.ArrayList;

import javax.imageio.ImageIO;
import javax.swing.ImageIcon;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.SwingConstants;

import gfx.ScaledImageLabel;
import util.Keyboard;

@SuppressWarnings("serial")
public class Scanner extends JFrame{	
	
	private volatile long counter = 0L;	
	private volatile boolean needsUpdate = false;
	
	private ScaledImageLabel imageLabel = new ScaledImageLabel();
	
	private String roomName;
	private JLabel title;
	private String normalTitle = "?";
	private JLabel subtext;
	private String normalSubtext = "<html><center>Please use the barcode scanner and your Student ID to sign in and out of this room.</center></html>";
	private String normalLocation = "/pictures/scan.png";
	
	public Scanner(String roomName, String hash) {
		super("Scanner Utility");
		this.getContentPane().setBackground( Color.BLACK);
        this.roomName = roomName.substring(0, 1).toUpperCase() + roomName.substring(1);
        this.normalTitle = "<html><center>" + this.roomName + " quick attendance system designed and developed by AJ Walter, YHS Class of 2017.</center></html>";
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
        final Keyboard k = new Keyboard(this, roomName, hash);
        this.addKeyListener(k);
        this.counter = System.currentTimeMillis();
        new Thread() {
        	public void run() {
          		while(true) {
        			if(counter < System.currentTimeMillis() - 3200 && needsUpdate) {
        				needsUpdate = false;
        				setImage(normalLocation);	
        				title.setText(normalTitle);
        				subtext.setText(normalSubtext);	
        				k.clearQue();
        			}
        			try {
						Thread.sleep(100L);
					} catch (InterruptedException e) {
						continue;
					}
        		}
        	}
        }.start();
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
	    
		title = new JLabel(normalTitle,SwingConstants.CENTER);
		Font labelFont = title.getFont();
		title.setFont(new Font(labelFont.getName(), Font.PLAIN, 64));
		title.setForeground(Color.RED);
		c.fill = GridBagConstraints.HORIZONTAL;
		c.gridheight = 1;
		c.gridx = 1;
	    c.gridy = 0;
	    p.add(title, c);
	    
		subtext = new JLabel(normalSubtext,SwingConstants.CENTER);
		labelFont = subtext.getFont();
		subtext.setFont(new Font(labelFont.getName(), Font.PLAIN, 40));
		subtext.setForeground(Color.WHITE);
		c.fill = GridBagConstraints.HORIZONTAL;
		c.gridheight = 1;
		c.gridx = 1;
	    c.gridy = 1;
	    p.add(subtext, c);
	    
	    BufferedImage image;
		try {
			image = ImageIO.read(getClass().getResourceAsStream(normalLocation));
			imageLabel.setIcon(new ImageIcon(image));
			c.fill = GridBagConstraints.HORIZONTAL;
		    c.weightx = 2.0;
		    c.weighty = 2.0;
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
	
	public String getHeaderText() {
		return title.getText();
	}
	
	public String getSubtitleText() {
		return subtext.getText();
	}
	
	public void setHeaderText(String text) {
		title.setText("<html><center>" + text + "</center></html>");
		counter = System.currentTimeMillis();
		needsUpdate = true;
	}
	
	public void setSubtitleText(String text) {
		subtext.setText("<html><center>" + text + "</center></html>");
		counter = System.currentTimeMillis();
		needsUpdate = true;
	}
	
	private ArrayList<String> loadedlocations = new ArrayList<>();
	private ArrayList<BufferedImage> loadedimages = new ArrayList<>();
	public void setImage(String location) {
		for(int i = 0; i < loadedlocations.size(); i++) {
			if(location.equals(loadedlocations.get(i))) {
				imageLabel.setIcon(new ImageIcon(loadedimages.get(i)));
				return;
			}
		}
		try {
			BufferedImage image = ImageIO.read(getClass().getResourceAsStream(location));
			loadedlocations.add(location);
			loadedimages.add(image);
			imageLabel.setIcon(new ImageIcon(image));
		}catch (Exception e) {
			System.out.println(location + " not found. Try refreshing your source.");
		}	
	}
}
