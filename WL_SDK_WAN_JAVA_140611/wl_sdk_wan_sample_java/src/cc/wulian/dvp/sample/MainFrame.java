package cc.wulian.dvp.sample;

import static cc.wulian.dvp.sample.callback.HandleCallBack.log;

import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

import javax.swing.JFrame;
import javax.swing.JList;
import javax.swing.JMenu;
import javax.swing.JMenuBar;
import javax.swing.JOptionPane;
import javax.swing.JScrollPane;
import javax.swing.SwingUtilities;
import javax.swing.UIManager;
import javax.swing.event.MenuEvent;
import javax.swing.event.MenuListener;

import org.dyno.visual.swing.layouts.Constraints;
import org.dyno.visual.swing.layouts.GroupLayout;
import org.dyno.visual.swing.layouts.Leading;

import cc.wulian.dvp.sample.callback.HandleCallBack;
import cc.wulian.dvp.sample.tools.DeviceTool;
import cc.wulian.dvp.sample.tools.HeartTask;
import cc.wulian.dvp.sample.tools.MacAddressUtil;
import cc.wulian.dvp.sample.ui.JListModel;
import cc.wulian.ihome.wan.NetSDK;
import cc.wulian.ihome.wan.entity.DeviceInfo;
import cc.wulian.ihome.wan.util.MD5Util;

//VS4E -- DO NOT REMOVE THIS LINE!
public class MainFrame extends JFrame
{
	private static final long serialVersionUID = 1L;
	private JMenu jMenu0;
	private JMenuBar jMenuBar0;
	private JMenu jMenu1;
	private JList jList;
	private JListModel mListModel;
	private JScrollPane jScrollPane0;
	private HandleCallBack mCallback;
	private HeartTask mHeartTask;
	private MacAddressUtil mMacAddress;
	private ExecutorService mExecutorService = Executors.newSingleThreadExecutor();
	private static final String PREFERRED_LOOK_AND_FEEL = "com.sun.java.swing.plaf.windows.WindowsLookAndFeel";

	public MainFrame()
	{
		initComponents();
	}

	private void initComponents(){
		setLayout(new GroupLayout());
		add(getJScrollPane0(), new Constraints(new Leading(3, 315, 10, 10), new Leading(-1, 430, 10, 10)));
		setJMenuBar(getJMenuBar0());
		setSize(328, 456);

		initUtil();
	}

	private void initUtil(){
		mMacAddress = new MacAddressUtil();
		mMacAddress.getSystemAllMacAddress();
		mCallback = new HandleCallBack(mListModel);
		System.out.println("NetSDK.init");// LOG
		NetSDK.init(mCallback);
		mHeartTask = new HeartTask();
		mHeartTask.startTimer();
	}

	private JScrollPane getJScrollPane0(){
		if (jScrollPane0 == null){
			jScrollPane0 = new JScrollPane(getJList0());
		}
		return jScrollPane0;
	}

	private JList getJList0(){
		if (jList == null){
			jList = new JList();
			mListModel = new JListModel();
			jList.setModel(mListModel);
			jList.setCellRenderer(mListModel);
			jList.addMouseListener(new MouseAdapter()
			{
				@Override
				public void mouseClicked( MouseEvent event ){
					int index = jList.locationToIndex(event.getPoint());
					jList.setSelectedIndex(index);
					if (index != -1){
						if (event.getButton() == MouseEvent.BUTTON1){
							mExecutorService.execute(new Runnable()
							{
								@Override
								public void run(){
									DeviceInfo deviceInfo = mListModel.getElementAt(jList.getSelectedIndex());
									DeviceTool.controlDevice(deviceInfo);
								}
							});
						}
					}
				}
			});
		}
		return jList;
	}

	private JMenu getJMenu1(){
		if (jMenu1 == null){
			jMenu1 = new JMenu();
			jMenu1.setText("Logout");
			jMenu1.addMenuListener(new MenuListener()
			{
				@Override
				public void menuSelected( MenuEvent e ){
					log("NetSDK.isConnected()", NetSDK.isConnected());
					System.out.println("NetSDK.disconnect");// LOG
					NetSDK.disconnect();
					log("NetSDK.isConnected()", NetSDK.isConnected());
					mListModel.removeAllDeviceInfo();
				}

				@Override
				public void menuDeselected( MenuEvent e ){
				}

				@Override
				public void menuCanceled( MenuEvent e ){
				}
			});
		}
		return jMenu1;
	}

	private JMenuBar getJMenuBar0(){
		if (jMenuBar0 == null){
			jMenuBar0 = new JMenuBar();
			jMenuBar0.add(getJMenu0());
			jMenuBar0.add(getJMenu1());
		}
		return jMenuBar0;
	}

	private JMenu getJMenu0(){
		if (jMenu0 == null){
			jMenu0 = new JMenu();
			jMenu0.setText("Login");
			jMenu0.addMenuListener(new MenuListener()
			{
				@Override
				public void menuSelected( MenuEvent e ){
					attemptSigin();
				}

				@Override
				public void menuDeselected( MenuEvent e ){
				}

				@Override
				public void menuCanceled( MenuEvent e ){
				}
			});
		}
		return jMenu0;
	}

	private void attemptSigin(){
		mExecutorService.execute(new Runnable()
		{
			@Override
			public void run(){
				if (mMacAddress.isVali()){
					log("NetSDK.isConnected()", NetSDK.isConnected());
					
					if (!mCallback.isConnectSev){
						System.out.println("NetSDK.connect");// LOG
						NetSDK.connect();
					}
					
					log("NetSDK.isConnected()", NetSDK.isConnected());

					System.out.println("NetSDK.sendConnectGwMsg");// LOG
					// TODO input your own gateway info
					// for example: gwID=00CFB834370E,gwPwd=34370E
					NetSDK.sendConnectGwMsg(mMacAddress.getMacAddress().firstElement(), "1", "gwID", MD5Util.encrypt("gwPwd"));
				}
				else{
					JOptionPane.showMessageDialog(null, "can not connect to internet");
					System.exit(0);
				}
			}
		});
	}

	private static void installLnF(){
		try{
			String lnfClassname = PREFERRED_LOOK_AND_FEEL;
			if (lnfClassname == null) lnfClassname = UIManager.getCrossPlatformLookAndFeelClassName();
			UIManager.setLookAndFeel(lnfClassname);
		}
		catch (Exception e){
			System.err.println("Cannot install " + PREFERRED_LOOK_AND_FEEL + " on this platform:" + e.getMessage());
		}
	}

	/**
	 * Main entry of the class. Note: This class is only created so that you can easily preview the result at runtime. It is not expected to be managed by the
	 * designer. You can modify it as you like.
	 */
	public static void main( String[] args ){
		installLnF();
		SwingUtilities.invokeLater(new Runnable()
		{
			public void run(){
				final MainFrame frame = new MainFrame();
				frame.setDefaultCloseOperation(MainFrame.DO_NOTHING_ON_CLOSE);
				frame.addWindowListener(new WindowAdapter()
				{
					@Override
					public void windowClosing( WindowEvent e ){
						int result = JOptionPane.showConfirmDialog(null, "Sure Exit", "Title", JOptionPane.OK_CANCEL_OPTION);
						if (result == 0){
							System.out.println("NetSDK.disconnect");// LOG
							NetSDK.disconnect();
							System.out.println("NetSDK.uninit");// LOG
							NetSDK.uninit();
							frame.mHeartTask.endTimer();
							System.exit(0);
						}
					}
				});
				frame.setTitle("wl_sdk_wan_sample_java");
				frame.getContentPane().setPreferredSize(frame.getSize());
				frame.pack();
				frame.setLocationRelativeTo(null);
				frame.setVisible(true);
			}
		});
	}
}
