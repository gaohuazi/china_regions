package util

import (
	"fmt"
	"io/ioutil"
	"net/http"
	"time"
)

func DownloadUrl(url string) string {
	resp, err := http.Get(url)
	if err != nil {
		fmt.Print(err)
		return ""
	}
	defer resp.Body.Close()

	bytes, err := ioutil.ReadAll(resp.Body)
	return string(bytes)
}

func CostTime() func() {
	startTime := time.Now()
	return func() {
		t := time.Since(startTime)
		fmt.Printf("耗时:%v\n", t)
	}
}

func WriteFile(path string, str string) {
	err := ioutil.WriteFile(path, []byte(str), 0755)
	if err != nil {
		fmt.Print(err)
	}
}
