package windows;

import java.awt.BorderLayout;
import java.awt.Component;
import java.awt.Container;
import java.awt.Dialog;
import java.awt.Dimension;

import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JCheckBox;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JTextField;
import javax.swing.border.TitledBorder;

import util.Keyboard;

@SuppressWarnings("serial")
public class Setup extends JDialog{
	public Setup(JFrame frame) {
		super(frame, "First time setup...", Dialog.ModalityType.APPLICATION_MODAL);
		this.setPreferredSize(new Dimension(220,340));
		this.setSize(new Dimension(220,480));
        this.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        addComponentsToPane(this.getContentPane());
        this.pack();
        this.setResizable(false);
        this.setLocationRelativeTo(null);
        this.setVisible(true);
        this.addKeyListener(new Keyboard());
	}

	public void addComponentsToPane(Container p) {
		final JPanel panel = new JPanel();
		BoxLayout panelLayout = new BoxLayout(panel,BoxLayout.Y_AXIS);
		final JPanel loginArea = new JPanel();
		BoxLayout loginAreaLayout = new BoxLayout(loginArea,BoxLayout.Y_AXIS);
		panel.setLayout(panelLayout);
		loginArea.setLayout(loginAreaLayout);
		
		TitledBorder title;
		JPanel borderPanel;
		JTextField text;
		JButton jb;
		JCheckBox check;
		
		title = BorderFactory.createTitledBorder("Room name:");
		text = new JTextField(15);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(text);
		loginArea.add(borderPanel);
		
		title = BorderFactory.createTitledBorder("Room password:");
		text = new JPasswordField(15);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(text);
		loginArea.add(borderPanel);
		
		title = BorderFactory.createTitledBorder("Your room information:");
		loginArea.setBorder(title);
		loginArea.setMaximumSize(new Dimension(this.getWidth(), 140));
		loginArea.setMinimumSize(new Dimension(this.getWidth(), 140));
		panel.add(loginArea);
		
		title = BorderFactory.createTitledBorder("Database Admin password:");
		text = new JPasswordField(15);
		borderPanel = new JPanel();
		borderPanel.setBorder(title);
		borderPanel.add(text);
		borderPanel.setMaximumSize(new Dimension(this.getWidth(), 60));
		borderPanel.setMinimumSize(new Dimension(this.getWidth(), 60));
		panel.add(borderPanel);
		
		check = new JCheckBox("Disable sign outs");
		check.setAlignmentX(Component.CENTER_ALIGNMENT);
		panel.add(check);
		
		JPanel spacerPanel = new JPanel();
		panel.add(spacerPanel);
		
		jb = new JButton("Create Database");
		jb.setAlignmentX(Component.CENTER_ALIGNMENT);
		jb.setMinimumSize(new Dimension(this.getWidth(), jb.getHeight()));
		jb.setMaximumSize(new Dimension(this.getWidth(), jb.getHeight()));
		panel.add(jb);
		jb = new JButton("Exit");
		jb.setAlignmentX(Component.CENTER_ALIGNMENT);
		jb.setMinimumSize(new Dimension(this.getWidth(), jb.getHeight()));
		jb.setMaximumSize(new Dimension(this.getWidth(), jb.getHeight()));
		panel.add(jb);
		jb = new JButton("Help");
		jb.setAlignmentX(Component.CENTER_ALIGNMENT);
		jb.setMinimumSize(new Dimension(this.getWidth(), jb.getHeight()));
		jb.setMaximumSize(new Dimension(this.getWidth(), jb.getHeight()));
		panel.add(jb);

		p.add(panel, BorderLayout.CENTER);
	}
	
	private void writeConfig() {
		
	}
}
