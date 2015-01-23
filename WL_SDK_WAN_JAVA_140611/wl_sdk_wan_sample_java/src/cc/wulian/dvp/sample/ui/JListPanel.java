package cc.wulian.dvp.sample.ui;

import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JSeparator;

import org.dyno.visual.swing.layouts.Constraints;
import org.dyno.visual.swing.layouts.GroupLayout;
import org.dyno.visual.swing.layouts.Leading;

//VS4E -- DO NOT REMOVE THIS LINE!
public class JListPanel extends JPanel
{
	private static final long serialVersionUID = 1L;
	public JLabel labelName;
	public JLabel labelData;
	private JSeparator jSeparator0;

	public JListPanel()
	{
		initComponents();
	}

	private void initComponents() {
		setLayout(new GroupLayout());
		add(getJLabelName(), new Constraints(new Leading(3, 149, 10, 10), new Leading(2, 25, 10, 10)));
		add(getJLabelData(), new Constraints(new Leading(162, 154, 10, 10), new Leading(2, 25, 10, 10)));
		add(getJSeparator0(), new Constraints(new Leading(0, 320, 12, 12), new Leading(30, 2, 0, 0)));
		setSize(320, 240);
	}

	private JSeparator getJSeparator0() {
		if (jSeparator0 == null) {
			jSeparator0 = new JSeparator();
		}
		return jSeparator0;
	}

	private JLabel getJLabelData(){
		if (labelData == null){
			labelData = new JLabel();
		}
		return labelData;
	}

	private JLabel getJLabelName(){
		if (labelName == null){
			labelName = new JLabel();
		}
		return labelName;
	}

}
