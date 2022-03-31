package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
	"log"

	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
)

type ListIngressLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewListIngressLogic(ctx context.Context, svcCtx *svc.ServiceContext) ListIngressLogic {
	return ListIngressLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *ListIngressLogic) ListIngress(req types.Request) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	//serviceList, _ := clientSet.CoreV1().Services(req.NameSpace).List(context.TODO(), metav1.ListOptions{})
	ingressList, _ := clientSet.ExtensionsV1beta1().Ingresses(req.NameSpace).List(context.TODO(), metav1.ListOptions{})
	items := make([]interface{}, 0, 1)
	for _, ingress := range ingressList.Items {
		item := make(map[string]interface{})
		item["namespace"] = ingress.Namespace
		item["name"] = ingress.Name
		item["host"] = ingress.Spec.Rules[0].Host
		item["ports"] = ingress.Spec.Rules[0].HTTP.Paths
		item["age"] = ingress.CreationTimestamp.Time.Format("2006-01-02 15:04:05")
		items = append(items, item)
	}
	fmt.Println("----------------items-----------------")
	fmt.Println(items)
	return nil, errorx.NewCodeError(200, "查询成功", items)
}
