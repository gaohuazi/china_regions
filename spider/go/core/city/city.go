package city

import (
	"bytes"
	"encoding/json"
	"fmt"
	"runtime"
	"sort"
	"strings"
	"sync"

	"../province"
	"../util"
)

var (
	waitGroup  sync.WaitGroup
	mapMutex   sync.RWMutex
	chanPids   chan int
	serviceUrl string = "https://fts.jd.com/area/get?fid=%d"
)

type Item = province.Item

type City struct {
	Data map[int][]Item
	Json string
}

func (this *City) ToJson() string {
	/* 	//key顺序会乱
	   	tempJson, _ := json.Marshal(&this.Data)
	   	this.Json = string(tempJson) */

	// key排序
	keySlice := make([]int, 0, len(this.Data))

	for key := range this.Data {
		keySlice = append(keySlice, key)
	}

	sort.Ints(keySlice)

	var buf bytes.Buffer
	buf.WriteString("{")
	l := len(keySlice)
	for i, k := range keySlice {
		buf.WriteString(fmt.Sprintf(`"%d":`, k))
		tempJson, _ := json.Marshal(this.Data[k])
		buf.Write(tempJson)
		if i != l-1 {
			buf.WriteString(",")
		}
	}

	buf.WriteString("}")
	this.Json = buf.String()
	return this.Json
}

func (this *City) parseChildren() {
	pid := <-chanPids

	fmt.Printf("pid:%v\n", pid)
	url := fmt.Sprintf(serviceUrl, pid)
	jsonStr := util.DownloadUrl(url)
	jsonStr = strings.Trim(jsonStr, "\n")
	if jsonStr != "[]" {
		items := make([]Item, 0)
		json.Unmarshal([]byte(jsonStr), &items)

		fmt.Printf("items:%v\n", items)

		mapMutex.Lock()
		this.Data[pid] = items
		mapMutex.Unlock()
	}
	waitGroup.Done()
}

func (this *City) Run(items []Item) string {
	runtime.GOMAXPROCS(runtime.NumCPU())

	/*
		1.将url添加到管道
		2.创建多个协成从管道中取出url，下载url，解析后放入data中
	*/

	chanPids = make(chan int, 2000)
	for _, v := range items {
		chanPids <- v.Id
	}
	fmt.Print("准备下载url\n")
	// 协程来下载url
	l := len(chanPids)
	for i := 0; i < l; i++ {
		waitGroup.Add(1)
		go this.parseChildren()
	}

	waitGroup.Wait()
	this.Json = this.ToJson()

	return this.Json
}
