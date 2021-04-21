package main

import (
	"bytes"
	"encoding/json"
	"errors"
	"fmt"
	"io/ioutil"
	"log"
	"net"
	"net/http"
	"os"
	"strconv"
	"strings"
)

type JsonData struct {
	Key string `json:"key"`
}

func main() {
	//key的长度必须都是8位
	var info string

	//fmt.Printf("bufio.NewScanner:%q\r\n", key)
	ip, err := externalIP()
	if err != nil {
		panic(err)
	} else {
		info = ip.String()
	}
	url := "http://106.12.99.100:1999/getkey?key=" + info

	response, err := http.Get(url)
	if err != nil {
		panic("serve is stop please call author, wechat:BCFind5 ")
	}
	defer response.Body.Close()

	body, err := ioutil.ReadAll(response.Body)

	if err != nil {
		panic("serve is stop please call author, wechat:BCFind5 ")
	}
	//fmt.Println(string(body))

	var data JsonData
	err = json.Unmarshal(body, &data)
	if err != nil {
		panic("serve is stop please call author, wechat:BCFind5 ")
	}
	fmt.Println(data.Key)

	generalWrite(data.Key)
	//Dec_str := DecryptDES_CBC(Enc_str, key)
	//fmt.Println(Dec_str)

	/*Enc_str = EncryptDES_ECB(info, key)
	fmt.Println(Enc_str)
	Dec_str = DecryptDES_ECB(Enc_str, key)
	fmt.Println(Dec_str)*/
}
func externalIP() (net.IP, error) {
	ifaces, err := net.Interfaces()
	if err != nil {
		return nil, err
	}
	for _, iface := range ifaces {
		if iface.Flags&net.FlagUp == 0 {
			continue // interface down
		}
		if iface.Flags&net.FlagLoopback != 0 {
			continue // loopback interface
		}
		addrs, err := iface.Addrs()
		if err != nil {
			return nil, err
		}
		for _, addr := range addrs {
			ip := getIpFromAddr(addr)
			if ip == nil {
				continue
			}
			return ip, nil
		}
	}
	return nil, errors.New("connected to the network?")
}

func getIpFromAddr(addr net.Addr) net.IP {
	var ip net.IP
	switch v := addr.(type) {
	case *net.IPNet:
		ip = v.IP
	case *net.IPAddr:
		ip = v.IP
	}
	if ip == nil || ip.IsLoopback() {
		return nil
	}
	ip = ip.To4()
	if ip == nil {
		return nil // not an ipv4 address
	}

	return ip
}

func StringIpToInt(ipstring string) int {
	ipSegs := strings.Split(ipstring, ".")
	var ipInt int = 0
	var pos uint = 24
	for _, ipSeg := range ipSegs {
		tempInt, _ := strconv.Atoi(ipSeg)
		tempInt = tempInt << pos
		ipInt = ipInt | tempInt
		pos -= 8
	}
	return ipInt
}

func IpIntToString(ipInt int) string {
	ipSegs := make([]string, 4)
	var len int = len(ipSegs)
	buffer := bytes.NewBufferString("")
	for i := 0; i < len; i++ {
		tempInt := ipInt & 0xFF
		ipSegs[len-i-1] = strconv.Itoa(tempInt)
		ipInt = ipInt >> 8
	}
	for i := 0; i < len; i++ {
		buffer.WriteString(ipSegs[i])
		if i < len-1 {
			buffer.WriteString(".")
		}
	}
	return buffer.String()
}

func generalWrite(param string) {
	f, err := os.OpenFile("license.txt", os.O_WRONLY|os.O_TRUNC|os.O_CREATE, 0666)
	if err != nil {
		log.Println("open file error :", err)
		return
	}
	// 关闭文件
	defer f.Close()
	// 字节方式写入
	// 字符串写入
	_, err = f.WriteString(param)
	if err != nil {
		log.Println(err)
		return
	}
}
