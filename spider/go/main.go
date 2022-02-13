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
	fmt.Println("采集完成...")
}
