package main

import (
	"fmt"

	"./core/spider"
	"./core/util"
)

// GO111MODULE=off go run main.go

func main() {
	defer util.CostTime()()
	spider := spider.NewSpider()
	spider.Run()
	fmt.Printf("\n\n采集完成，已下载到%s下的json与sql目录\n", spider.OutPath)
}
