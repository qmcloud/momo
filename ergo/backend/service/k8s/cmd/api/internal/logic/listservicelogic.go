package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
	"log"
)

type ListServiceLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewListServiceLogic(ctx context.Context, svcCtx *svc.ServiceContext) ListServiceLogic {
	return ListServiceLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *ListServiceLogic) ListService(req types.Request) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	serviceList, _ := clientSet.CoreV1().Services(req.NameSpace).List(context.TODO(), metav1.ListOptions{})
	items := make([]interface{}, 0, 1)
	for _, svc := range serviceList.Items {
		item := make(map[string]interface{})
		item["namespace"] = svc.Namespace
		item["name"] = svc.Name
		item["type"] = svc.Spec.Type
		item["cluster_ip"] = svc.Spec.ClusterIP
		item["ports"] = svc.Spec.Ports
		//ports, _ := json.Marshal(svc.Spec.Ports)
		//item["ports"] = string(ports)
		item["age"] = svc.CreationTimestamp.Time.Format("2006-01-02 15:04:05")
		//selector, _ := json.Marshal(svc.Spec.Selector)
		//item["selector"] = string(selector)
		item["selector"] = svc.Spec.Selector
		item["labels"] = svc.Labels

		//item["status"] = ns.Status.Phase
		items = append(items, item)
	}
	fmt.Println("----------------items-----------------")
	fmt.Println(items)
	return nil, errorx.NewCodeError(200, "查询成功", items)
}
