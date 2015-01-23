package cc.wulian.dvp.sample.callback;

import java.util.Set;

import cc.wulian.dvp.sample.ui.JListModel;
import cc.wulian.ihome.wan.MessageCallback;
import cc.wulian.ihome.wan.NetSDK;
import cc.wulian.ihome.wan.entity.DeviceEPInfo;
import cc.wulian.ihome.wan.entity.DeviceInfo;
import cc.wulian.ihome.wan.entity.GatewayInfo;
import cc.wulian.ihome.wan.util.ResultUtil;

public class HandleCallBack implements MessageCallback
{
	private static final String TAG = HandleCallBack.class.getSimpleName();

	public boolean isConnectSev = false;
	public boolean isConnectGw = false;
	private JListModel mListModel;

	public HandleCallBack( JListModel mListModel )
	{
		this.mListModel = mListModel;
	}

	@Override
	public void ConnectServer( int result ){
		log("ConnectServer", "result", result);

		isConnectSev = ResultUtil.RESULT_SUCCESS == result;
	}

	@Override
	public void ConnectGateway( int result, String gwID, GatewayInfo gwInfo ){
		log("ConnectGateway", "result", result, "gwID", gwID, "GatewayInfo", gwInfo.toString());
		checkGatewayResult(result);

		if (ResultUtil.RESULT_SUCCESS == result){
			System.out.println("NetSDK.sendRefreshDevListMsg");// LOG
			NetSDK.sendRefreshDevListMsg(gwID, null);
			isConnectGw = true;
		}
		else{
			isConnectGw = false;
		}
	}

	private void checkGatewayResult( int result ){
		String msg;
		switch (result){
			case ResultUtil.EXC_GW_OFFLINE :
				msg = "gateway offline";
				break;
			case ResultUtil.EXC_GW_USER_WRONG :
				msg = "wrong gateway id";
				break;
			case ResultUtil.EXC_GW_PASSWORD_WRONG :
				msg = "wrong gateway password";
				break;
			case ResultUtil.EXC_GW_REMOTE_SERIP :
				msg = "gateway in other server";
				break;
			case ResultUtil.EXC_GW_OVER_CONNECTION :
				msg = "server connection has full";
				break;
			default :
				msg = null;
				break;
		}
		log(msg);
	}

	@Override
	public void DisConnectGateway( int result, String gwID ){
		log("DisConnectGateway", "result", result, "gwID", gwID);

		isConnectGw = false;
	}

	@Override
	public void GatewayData( int result, String gwID ){
		log("GatewayData", "result", result, "gwID", gwID);
	}

	@Override
	public void GatewayDown( String gwID ){
		log("GatewayDown", "gwID", gwID);
	}

	@Override
	public void DeviceUp( DeviceInfo devInfo, Set<DeviceEPInfo> devEPInfoSet ){
		String devID = devInfo.getDevID();
		log("DeviceUp", "gwID", devInfo.getGwID(), "devID", devID);

		devInfo.setDevEPInfo((DeviceEPInfo) devEPInfoSet.toArray()[0]);
		mListModel.addDeviceInfo(devID, devInfo);
	}

	@Override
	public void DeviceDown( String gwID, String devID ){
		log("DeviceDown", "gwID", gwID, "devID", devID);

		mListModel.removeDeviceInfo(devID);
	}

	@Override
	public void DeviceData( String gwID, String devID, String devType, DeviceEPInfo devEPInfo ){
		log("DeviceData", "gwID", gwID, "devID", devID, "devType", devType, "DeviceInfo", devEPInfo.toString());

		DeviceInfo devInfo = new DeviceInfo();
		devInfo.setGwID(gwID);
		devInfo.setDevID(devID);
		devInfo.setDevEPInfo(devEPInfo);
		mListModel.setDeviceInfo(devID, devInfo);
	}

	@Override
	public void HandleException( String gwID, Exception e ){
		log("HandleException", "gwID", gwID, "Exception", e.getCause() + e.getMessage());
	}

	public static void log( Object... msgs ){
		System.out.print(TAG);
		for (Object msg : msgs){
			System.out.print(" : " + msg);// LOG
		}
		System.out.println();// LOG
	}
}