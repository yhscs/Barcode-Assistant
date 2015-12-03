package util;

import java.nio.ByteBuffer;
import java.nio.CharBuffer;
import java.nio.charset.Charset;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.Arrays;

public class Constants {
	private Constants() throws InstantiationException{
		throw new InstantiationException();
	}
	
	public static final String DATABASE_PHP_URL="http://attendance.yhscs.us/db.php";
	
	public static final String INDEX_KEY_ROOM="ROOM";
	public static final String INDEX_KEY_ROOM_PASSWORD="ROOM_PASSWORD";
	public static final String INDEX_KEY_ADMIN_PASSWORD="ADMINISTRATOR_PASSWORD";
	public static final String INDEX_KEY_SALT = "SALTY_MC_SALTER";
	
	/**
	 * Credit http://stackoverflow.com/questions/5513144/converting-char-to-byte
	 * @param chars to convert to bytes
	 * @return byte array in UTF-8
	 */
	public static byte[] toBytes(char[] chars) {
	    CharBuffer charBuffer = CharBuffer.wrap(chars);
	    ByteBuffer byteBuffer = Charset.forName("UTF-8").encode(charBuffer);
	    byte[] bytes = Arrays.copyOfRange(byteBuffer.array(),
	            byteBuffer.position(), byteBuffer.limit());
	    Arrays.fill(charBuffer.array(), '\u0000'); // clear sensitive data
	    Arrays.fill(byteBuffer.array(), (byte) 0); // clear sensitive data
	    return bytes;
	}
	
	/**
	 * Credit http://howtodoinjava.com/2013/07/22/how-to-generate-secure-password-hash-md5-sha-pbkdf2-bcrypt-examples/
	 * @param passwordToHash
	 * @return a hashed string in sha512
	 */
	public static String getSHA512Hash(byte[] passwordToHash)
	{
		String generatedPassword = null;
		try {
			MessageDigest md = MessageDigest.getInstance("SHA-512");
			byte[] bytes = md.digest(passwordToHash);
			StringBuilder sb = new StringBuilder();
			for(int i=0; i< bytes.length ;i++)
			{
				sb.append(Integer.toString((bytes[i] & 0xff) + 0x100, 16).substring(1));
			}
			generatedPassword = sb.toString();
		} 
		catch (NoSuchAlgorithmException e) 
		{
			System.err.print("");
			System.err.println(e);
		}
		return generatedPassword;
	}
}
