package cc.wulian.dvp.sample.ui;

import java.awt.Color;
import java.awt.Component;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.swing.AbstractListModel;
import javax.swing.JList;
import javax.swing.ListCellRenderer;

import cc.wulian.dvp.sample.tools.DeviceTool;
import cc.wulian.ihome.wan.entity.DeviceInfo;
import cc.wulian.ihome.wan.util.StringUtil;

public class JListModel extends AbstractListModel implements ListCellRenderer
{
	private static final long serialVersionUID = 1L;

	public Map<String, DeviceInfo> mDeviceInfoMap = new HashMap<String, DeviceInfo>();
	private List<String> mDevices = new ArrayList<String>();

	public void addDeviceInfo( String devID, DeviceInfo deviceInfo ){
		if (!mDevices.contains(devID)) mDevices.add(devID);
		mDeviceInfoMap.put(devID, deviceInfo);
		int index = mDevices.indexOf(devID);
		fireIntervalAdded(this, index, index);

		// only refresh list ui
		setDeviceInfo(devID, deviceInfo);
	}

	public void setDeviceInfo( String devID, DeviceInfo deviceInfo ){
		// start 2014-02-12 fix control feedback show
		// mDeviceInfoMap.put(devID, deviceInfo);
		DeviceInfo oldInfo = mDeviceInfoMap.get(devID);
		if (oldInfo == null){
			oldInfo = deviceInfo;
			mDeviceInfoMap.put(devID, oldInfo);
		}
		oldInfo.setDevEPInfo(deviceInfo.getDevEPInfo());
		// end
		
		int index = mDevices.indexOf(devID);
		fireContentsChanged(this, index, index);
	}

	public void removeDeviceInfo( String devID ){
		int index = mDevices.indexOf(devID);
		fireIntervalRemoved(this, index, index);
		mDeviceInfoMap.remove(devID);
		mDevices.remove(devID);
	}

	public void removeAllDeviceInfo(){
		int index = getSize() - 1;
		if (index != -1){
			fireIntervalRemoved(this, 0, getSize() - 1);
			mDeviceInfoMap.clear();
			mDevices.clear();
		}
	}

	@Override
	public int getSize(){
		return mDevices.size();
	}

	@Override
	public DeviceInfo getElementAt( int index ){
		return mDeviceInfoMap.get(mDevices.get(index));
	}

	@Override
	public Component getListCellRendererComponent( JList list, Object value, int index,
			boolean isSelected, boolean cellHasFocus ){
		DeviceInfo deviceInfo = (DeviceInfo) value;

		// device info
		String devName = deviceInfo.getName();
		String epType = deviceInfo.getDevEPInfo().getEpType();
		String epData = deviceInfo.getDevEPInfo().getEpData();
		String epStatus = deviceInfo.getDevEPInfo().getEpStatus();
		String devDataParsed = DeviceTool.getDevDataText(epType, epData, epStatus);
		if (StringUtil.isNullOrEmpty(devName)) devName = DeviceTool.getDevDefaultNameByType(epType);

		JListPanel listPanel = new JListPanel();

		// set view
		boolean isOn = "1".equals(DeviceTool.getSendCtrlStatusByByTypeAndData(epData, epType, epStatus,
				false) ? "1" : "2");
		if (isOn){
			listPanel.labelData.setText("<html><font color='red'>" + devDataParsed + "</font></html>");
		}
		else{
			listPanel.labelData.setText("<html><font color='gray'>" + devDataParsed + "</font></html>");
		}
		listPanel.labelName.setText(devName);

		Color bg = null;
		Color fg = null;

		JList.DropLocation dropLocation = list.getDropLocation();
		if (dropLocation != null && !dropLocation.isInsert() && dropLocation.getIndex() == index){

			bg = Color.gray;
			fg = Color.DARK_GRAY;

			isSelected = true;
		}

		if (isSelected){
			listPanel.setBackground(bg == null ? list.getSelectionBackground() : bg);
			listPanel.setForeground(fg == null ? list.getSelectionForeground() : fg);
		}
		else{
			listPanel.setBackground(list.getBackground());
			listPanel.setForeground(list.getForeground());
		}

		return listPanel;
	}
}